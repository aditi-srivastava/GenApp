package airavata;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import org.apache.airavata.api.Airavata;
import org.apache.airavata.api.client.AiravataClientFactory;
import org.apache.airavata.model.appcatalog.appinterface.DataType;
import org.apache.airavata.model.appcatalog.appinterface.InputDataObjectType;
import org.apache.airavata.model.appcatalog.appinterface.OutputDataObjectType;
import org.apache.airavata.model.error.AiravataClientConnectException;
import org.apache.airavata.model.util.ExperimentModelUtil;
import org.apache.airavata.model.util.ProjectModelUtil;
import org.apache.airavata.model.workspace.Project;
import org.apache.airavata.model.workspace.experiment.ComputationalResourceScheduling;
import org.apache.airavata.model.workspace.experiment.Experiment;
import org.apache.airavata.model.workspace.experiment.ExperimentState;
import org.apache.airavata.model.workspace.experiment.ExperimentStatus;
import org.apache.airavata.model.workspace.experiment.UserConfigurationData;
import org.apache.thrift.TException;
import org.json.simple.JSONObject;

import util.AppConfig;

public class ExperimentUtils {
    private String THRIFT_SERVER_HOST;
    private int THRIFT_SERVER_PORT;
    private String DEFAULT_GATEWAY;
    private String OWNER;
    private String TOKEN;
    private String PROJECT_ACCOUNT_NAME;
    private Airavata.Client airavataClient;
    
    public ExperimentUtils() throws AiravataClientConnectException{
        init();
        CreateAiravataClient();
    }
    
    private void init(){
        
        JSONObject airvata_properties = AppConfig.getAiravataProperties();
        THRIFT_SERVER_HOST = (String) airvata_properties.get("server");
        THRIFT_SERVER_PORT = Integer.parseInt((long) airvata_properties.get("port")+"");
        DEFAULT_GATEWAY = (String) airvata_properties.get("gateway");
        OWNER = (String) airvata_properties.get("login");
        TOKEN = (String) airvata_properties.get("credentialStoreToken");
        PROJECT_ACCOUNT_NAME = (String) airvata_properties.get("projectAccount");
    }
    
   public void CreateAiravataClient() throws AiravataClientConnectException{
       airavataClient = AiravataClientFactory.createAiravataClient(THRIFT_SERVER_HOST, (int) THRIFT_SERVER_PORT);
   }
   
   public String CreateProject(String projectName) throws TException{
       Project project = ProjectModelUtil.createProject(projectName, OWNER, "GenApp module test project");
       return airavataClient.createProject(DEFAULT_GATEWAY, project);
   }
   
   public String CreateExperiment(String expName, String projectId, String appId, String inputJson) throws TException{
       List<InputDataObjectType> exInputs = new ArrayList<InputDataObjectType>();
       InputDataObjectType input = new InputDataObjectType();
       input.setName("json_input");
       input.setType(DataType.STRING);
       input.setValue(inputJson);
       exInputs.add(input);

       List<OutputDataObjectType> exOut = new ArrayList<OutputDataObjectType>();
       OutputDataObjectType output = new OutputDataObjectType();
       output.setName("output_json");
       output.setType(DataType.STDOUT);
       output.setValue("");
       exOut.add(output);
       
       Experiment genAppExperiment =
               ExperimentModelUtil.createSimpleExperiment(projectId, OWNER, expName, "GenApp Module Experiment", appId, exInputs);
       genAppExperiment.setExperimentOutputs(exOut);
       Map<String, String> cmrf = airavataClient.getAvailableAppInterfaceComputeResources(appId);
       if(cmrf.containsValue(THRIFT_SERVER_HOST)){
           ComputationalResourceScheduling scheduling = ExperimentModelUtil.createComputationResourceScheduling(
                   cmrf.get(THRIFT_SERVER_HOST), 1, 1, 1, "normal", 30, 0, 1, PROJECT_ACCOUNT_NAME);
        scheduling.setResourceHostId(cmrf.get(THRIFT_SERVER_HOST));
        UserConfigurationData userConfigurationData = new UserConfigurationData();
        userConfigurationData.setAiravataAutoSchedule(false);
        userConfigurationData.setOverrideManualScheduledParams(false);
        userConfigurationData.setComputationalResourceScheduling(scheduling);
        genAppExperiment.setUserConfigurationData(userConfigurationData);
       }
       return airavataClient.createExperiment(DEFAULT_GATEWAY, genAppExperiment);
   }
   
   public void launchExperiment(String expId) throws TException{
       airavataClient.launchExperiment(expId, "");
   }
   
   public String getOutput(String expId) throws TException, InterruptedException{
       ExperimentState state = null;
       int waitTime = 1;
       while(!(state = getExperimentState(expId)).equals(ExperimentState.COMPLETED)){
           if(state.equals(ExperimentState.CANCELED) 
                   || state.equals(ExperimentState.SUSPENDED) 
                   || state.equals(ExperimentState.FAILED)){
       
             return "{\"error\": \""+state+"\"}";
           }
           Thread.sleep(waitTime);
           waitTime++;
       }
       return airavataClient.getExperimentOutputs(expId).get(0).getValue();
   }
   
   private ExperimentState getExperimentState(String expId) throws TException{
       ExperimentStatus status = airavataClient.getExperimentStatus(expId);
       return status.getExperimentState();
   }

}
