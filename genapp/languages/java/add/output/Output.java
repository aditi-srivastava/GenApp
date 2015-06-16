package output;

import java.util.HashMap;

import javafx.scene.control.TextField;
import javafx.scene.layout.HBox;
import javafx.scene.text.Text;

public class Output {
    public static HBox getOutput(HashMap<String, String> data){
        HBox output = new HBox();
        output.getStyleClass().add("output");
        Text outTitle = new Text(data.get("label"));
        TextField textField = new TextField();
        output.getChildren().addAll(outTitle, textField);
        return output;
    }
}
