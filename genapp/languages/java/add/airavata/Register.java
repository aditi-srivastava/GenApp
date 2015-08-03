package airavata;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import org.apache.airavata.api.Airavata;
import org.apache.airavata.api.client.AiravataClientFactory;
import org.apache.airavata.model.appcatalog.appdeployment.ApplicationParallelismType;
import org.apache.airavata.model.appcatalog.appinterface.DataType;
import org.apache.airavata.model.appcatalog.appinterface.InputDataObjectType;
import org.apache.airavata.model.appcatalog.appinterface.OutputDataObjectType;
import org.apache.airavata.model.appcatalog.computeresource.ComputeResourceDescription;
import org.apache.airavata.model.appcatalog.computeresource.LOCALSubmission;
import org.apache.airavata.model.appcatalog.computeresource.ResourceJobManager;
import org.apache.airavata.model.appcatalog.computeresource.ResourceJobManagerType;
import org.apache.airavata.model.appcatalog.gatewayprofile.ComputeResourcePreference;
import org.apache.airavata.model.appcatalog.gatewayprofile.GatewayResourceProfile;
import org.apache.airavata.model.error.AiravataClientConnectException;
import org.apache.airavata.model.error.AiravataClientException;
import org.apache.airavata.model.error.AiravataSystemException;
import org.apache.airavata.model.error.InvalidRequestException;
import org.apache.thrift.TException;
import org.json.simple.JSONObject;

import util.AppConfig;

public class Register {
    
    private static String THRIFT_SERVER_HOST;
    private static int THRIFT_SERVER_PORT;
    private static String DEFAULT_GATEWAY;
    private static JSONObject airvata_properties;
    private Airavata.Client airavataClient;
    private String hostId ;
    private List<String> modules;
    private HashMap<String, String> moduleIds;
    private String executablePath;

    public static void main(String[] args) throws AiravataClientConnectException, TException{
       Register registerGenApp = new Register();
       registerGenApp.init();
       registerGenApp.register();

    }
    
    public void register() throws AiravataClientConnectException, InvalidRequestException, AiravataClientException, AiravataSystemException, TException{
        airavataClient = AiravataClientFactory.createAiravataClient(THRIFT_SERVER_HOST, (int) THRIFT_SERVER_PORT);
        addGateway();
        boolean gatewayProfileRegistered = registerHost();
        if(!gatewayProfileRegistered){
            registerGatewayProfile();
        }
        modules = getUnregisteredModules();
        if(modules.size()>0){
            moduleIds = registerApplicationModules();
            registerApplicationDeployments();
            registerApplicationInterfaces();
        }
    }
    
    private void init(){
        JSONObject appConfig = AppConfig.getAppConfig();
        if(appConfig.get("resourcedefault").equals("airavata")){
            JSONObject resources = (JSONObject)appConfig.get("resources");
            airvata_properties = (JSONObject) resources.get("airavata_properties");
            THRIFT_SERVER_HOST = (String) airvata_properties.get("AIRAVATA_SERVER");
            THRIFT_SERVER_PORT = Integer.parseInt((long) airvata_properties.get("AIRAVATA_PORT")+"");
            DEFAULT_GATEWAY = (String) airvata_properties.get("AIRAVATA_GATEWAY");
        }
        executablePath = ModulesUtils.getExecutablePath();
    }
    
    private void addGateway() throws InvalidRequestException, AiravataClientException, AiravataSystemException, TException{
        if(!airavataClient.isGatewayExist(DEFAULT_GATEWAY)){
           airavataClient.addGateway(RegisterUtils.createGateway(airvata_properties)); 
        }
    }
    
    private boolean registerHost() throws InvalidRequestException, AiravataClientException, AiravataSystemException, TException{
        boolean gatewayProfileRegistered = false;
        if(RegisterUtils.isGatewayProfileRegistered(airavataClient, DEFAULT_GATEWAY)){
            List<ComputeResourcePreference> cmrf = airavataClient.getGatewayResourceProfile(DEFAULT_GATEWAY)
                                                            .getComputeResourcePreferences();
            hostId = cmrf.get(0).getComputeResourceId();
            gatewayProfileRegistered = true; 
        }else{
                System.out.println("\n #### Registering Localhost Computational Resource #### \n");
                ComputeResourceDescription computeResourceDescription = RegisterUtils.
                        createComputeResourceDescription("localhost", "LocalHost", null, null);
                hostId = airavataClient.registerComputeResource(computeResourceDescription);
                ResourceJobManager resourceJobManager = RegisterUtils.
                        createResourceJobManager(ResourceJobManagerType.FORK, null, null, null);
                LOCALSubmission submission = new LOCALSubmission();
                submission.setResourceJobManager(resourceJobManager);
                String localSubmission = airavataClient.addLocalSubmissionDetails(hostId, 1, submission);
                System.out.println(localSubmission);
                System.out.println("LocalHost Resource Id is " + hostId);
                gatewayProfileRegistered = false;
        }
        return gatewayProfileRegistered;
        
    }
    
    private void registerGatewayProfile() throws TException {
        ComputeResourcePreference localhostResourcePreference = RegisterUtils.
                createComputeResourcePreference(hostId, "executable_modules", false, null, null, null, executablePath + "/..");
        GatewayResourceProfile gatewayResourceProfile = new GatewayResourceProfile();
        gatewayResourceProfile.setGatewayID(DEFAULT_GATEWAY);
        gatewayResourceProfile.addToComputeResourcePreferences(localhostResourcePreference);
        airavataClient.registerGatewayResourceProfile(gatewayResourceProfile);
    }
    
    private List<String> getUnregisteredModules() throws TException{
        List<String> unregistered = new ArrayList<String>();
        List<String> allModules = ModulesUtils.getModulesNames();
        Map<String, String> registeredModules = airavataClient.getAllApplicationInterfaceNames(DEFAULT_GATEWAY);
        Iterator<String> modulesIt = allModules.iterator();
        while(modulesIt.hasNext()){
            String id = modulesIt.next();
            if(registeredModules.containsKey(id+"_java")){
                if(registeredModules.get(id+"_java").equals(id)){
                    System.out.println("### "+id+" Already registered for java");
                }else{
                    unregistered.add(id);
                }
            }else{
                unregistered.add(id);
            }
        }
        return unregistered;
    }
    
    private HashMap<String, String> registerApplicationModules() throws TException{
        HashMap<String, String> id = new HashMap<String, String>();
        Iterator<String> modulesIt = modules.iterator();
        while (modulesIt.hasNext()) {
            String module = modulesIt.next();
            id.put(module, airavataClient.registerApplicationModule(DEFAULT_GATEWAY, 
                            RegisterUtils.createApplicationModule(module, "1.0", "GenApp"+module+"module")));
            
        }
        return id;
    }
    
    private void registerApplicationDeployments() throws TException{
        System.out.println("#### Registering Application Deployments on "+THRIFT_SERVER_HOST+" ####");
        Iterator<String> moduleName = modules.iterator();
        while (moduleName.hasNext()) {
            String name = moduleName.next();
            String deployId = airavataClient.registerApplicationDeployment(DEFAULT_GATEWAY,
                    RegisterUtils.createApplicationDeployment(moduleIds.get(name), hostId,
                            executablePath + "/"+name, ApplicationParallelismType.SERIAL, name+" application description"));
            System.out.println("Successfully registered "+name+" application on localhost, application Id = "+deployId);
            
        }
    }
    
    private void registerApplicationInterfaces() {
        Iterator<String> it = modules.iterator();
        while(it.hasNext()){
            try {
                String id = it.next();
                System.out.println("#### Registering "+id+" Interface ####");
                List<String> appModules = new ArrayList<String>();
                appModules.add(moduleIds.get(id));

                InputDataObjectType input1 = RegisterUtils.createAppInput("Input_JSON", "{}",
                        DataType.STRING, null, false, "JSON String", null);

                List<InputDataObjectType> applicationInputs = new ArrayList<InputDataObjectType>();
                applicationInputs.add(input1);

                OutputDataObjectType output1 = RegisterUtils.createAppOutput("JSON_Output",
                        "{}", DataType.STRING);

                List<OutputDataObjectType> applicationOutputs = new ArrayList<OutputDataObjectType>();
                applicationOutputs.add(output1);

                String InterfaceId = airavataClient.registerApplicationInterface(DEFAULT_GATEWAY,
                        RegisterUtils.createApplicationInterfaceDescription(id , id+" application description",
                                appModules, applicationInputs, applicationOutputs));
                System.out.println(id+" Application Interface Id " + InterfaceId);

            } catch (TException e) {
                e.printStackTrace();
            }
        }

    }

}
