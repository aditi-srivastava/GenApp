package modules;

import java.util.HashMap;
import java.util.Iterator;

import output.Output;
import util.HandleResult;
import util.JobRun;

import data.__menu:modules:id___data;

import input.Input;
import org.json.simple.JSONObject;
import javafx.event.EventHandler;
import javafx.scene.Node;
import javafx.scene.control.Button;
import javafx.scene.input.MouseEvent;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.scene.text.Text;

public class __menu:modules:id__ extends VBox implements EventHandler<MouseEvent>{
    private VBox inputBox, outputBox;

    public __menu:modules:id__(){
        this.getStyleClass().add("module");
        inputBox = setInputs();
        outputBox = setOutputs();
        outputBox.getStyleClass().addAll("outputBox", "outputs");
        inputBox.getStyleClass().add("inputs");
        this.setId("__menu:modules:label___module");
        this.getChildren().addAll(inputBox, outputBox);
    }
    
    private VBox setInputs(){
        VBox inputs = new VBox();
        for (HashMap<String, String> data : __menu:modules:id___data.input) {
            HBox input = Input.getInputInterface(data, inputs);
            inputs.getChildren().add(input);    
        }
        HBox bt = Input.getSubmitButton();
        Iterator<Node> btIt = bt.getChildren().iterator();
        while(btIt.hasNext()){
            btIt.next().setOnMouseClicked(this);
        }
        inputs.getChildren().add(bt);
        return inputs;
    }
    
    private VBox setOutputs(){
        VBox output = new VBox();
        HBox errorBox = new HBox();
        Text error = new Text("");
        error.getStyleClass().add("error");
        error.setId("error");
        errorBox.getChildren().add(error);
        errorBox.setId("errorBox");
        output.getChildren().add(errorBox);
        for (HashMap<String, String> data : __menu:modules:id___data.output) {
            HBox out = Output.getOutput(data);
            output.getChildren().add(out);    
        }
        return output;
    }

    @Override
    public void handle(MouseEvent event) {
        Button bt = (Button) event.getSource();
        switch(bt.getId()){
        case "submit":
            JSONObject data = HandleResult.prepareToRun("__menu:modules:id___","__executable_path:java__/__menu:modules:id__",inputBox);
            String[] cmd = new String[]{
                    "/bin/sh",
                    "-c",
                    data.get("cmds")+"",
            };
            
            JobRun jb = new JobRun(cmd,data, outputBox);
            Thread jobThread = new Thread(jb);
            jobThread.start();
            break;
        case "reset":
            break;
        
        }
        
    }
    
}