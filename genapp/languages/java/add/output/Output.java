package output;

import java.util.HashMap;

import javafx.scene.Node;
import javafx.scene.control.TextField;
import javafx.scene.layout.HBox;
import javafx.scene.text.Text;

public class Output {
    public static HBox getOutput(HashMap<String, String> data){
        HBox output = new HBox();
        output.getStyleClass().add("output");
        Text outTitle = new Text(data.get("label"));
        outTitle.getStyleClass().add("label");
        TextField textField = new TextField();
        textField.setId(data.get("id"));
        output.getChildren().addAll(outTitle, textField);
        return output;
    }
    
    public static void setOutput(Node nd, Object output){
        switch(nd.getTypeSelector()){
        case "TextField":
                setTextFieldOutput((TextField) nd, output.toString());
            break;
        }
    }

    private static void setTextFieldOutput(TextField nd, String output) {
        nd.setText(output);
        
    }
}
