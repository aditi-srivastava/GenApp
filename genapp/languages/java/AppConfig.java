package util;

import java.io.FileReader;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

public class AppConfig {

    public static JSONObject getAppConfig(){
        JSONObject appConfig = null;
        JSONParser parser = new JSONParser();
        try {        
             appConfig = (JSONObject) parser.parse(new FileReader("__appconfig__"));
        
        } catch (Exception e) {
            e.printStackTrace();
        }
        return appConfig;
    }
    
    public static boolean runAiravata(){
        boolean airavata = false;
        JSONObject config = getAppConfig();
        String resource = (String) config.get("resourcedefault");
        if(resource.equals("airavata")){
            JSONObject jsonResource = (JSONObject) config.get("resources");
            if(jsonResource.containsKey(resource)){
                String airavataRun = (String) jsonResource.get(resource);
                if(airavataRun.equals("airavatarun-0.15")){
                    airavata = true;
                }
            }
        }
        return airavata;
    }

}
