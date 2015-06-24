package util;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import org.json.simple.JSONObject;

import javafx.scene.layout.VBox;

import states.ApplicationStates;

public class JobRun implements Runnable{
    public String JOB_STATE = null;
    private VBox outputs;
    private String[] command;
    private JSONObject data;
    public JobRun(String[] command,JSONObject data, VBox outputs){
        JOB_STATE = ApplicationStates.JOB_STATES[0];
        this.command = command;
        this.outputs = outputs;
        this.data = data;
    }
    
    public static String readInputStream(InputStream input) throws IOException{
        String fileData = "";
        BufferedReader reader = new BufferedReader(new InputStreamReader(input));
        String line = null;
        while ((line = reader.readLine()) != null) {
           fileData +=line;
        }
        return fileData;
    }
    
    @Override
    public void run() {
        try {
            ProcessBuilder pb = new ProcessBuilder(command);
            Process p = pb.start();
                
                JSONObject json = HandleResult.getJSON(readInputStream(p.getInputStream()));
                HandleResult.wrtieFile(json+"", data.get("base_dir")+"/_output_"+data.get("uuid"));
                HandleResult.setOutput(outputs, json);

        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Error file not found");
        }
        
    }
    
}
