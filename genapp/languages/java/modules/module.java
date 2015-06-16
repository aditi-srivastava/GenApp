package modules;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import output.Output;
import data.ModulesData;
import input.Input;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;

public class __menu:modules:id__ extends VBox{
    private VBox inputBox, outputBox;
    private List<HashMap<String, String>> outputs = new ArrayList<HashMap<String,String>>();

    public __menu:modules:id__(){
        this.getStyleClass().add("module");
        inputBox = setInputs();
        outputBox = setOutputs();
        outputBox.getStyleClass().addAll("outputBox", "outputs");
        inputBox.getStyleClass().add("inputs");
        this.setId("__menu:modules:label___Module");
        this.getChildren().addAll(inputBox, outputBox);
    }
    
    private VBox setInputs(){
        VBox inputs = new VBox();
        for (HashMap<String, String> data : ModulesData.__menu:modules:id__) {
            if(data.get("role").equals("input")){
            HBox input = Input.getInput(data);
            inputs.getChildren().add(input);    
            }else if(data.get("role").equals("output")){
                outputs.add(data);
            }
        }
        inputs.getChildren().add(Input.getSubmitButton());
        return inputs;
    }
    
    private VBox setOutputs(){
        VBox output = new VBox();
        for (HashMap<String, String> data : outputs) {
            HBox out = Output.getOutput(data);
            output.getChildren().add(out);    
        }
        return output;
    }
    
}
