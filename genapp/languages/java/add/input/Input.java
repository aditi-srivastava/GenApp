package input;

import java.util.HashMap;

import javafx.beans.value.ChangeListener;
import javafx.beans.value.ObservableValue;
import javafx.scene.control.Button;
import javafx.scene.control.CheckBox;
import javafx.scene.control.TextField;
import javafx.scene.layout.HBox;
import javafx.scene.text.Text;

public class Input {
    public static HBox getSubmitButton(){
        Button submit = new Button("Submit");
        submit.getStyleClass().add("button");
        Button reset = new Button("Reset to default Values");
        reset.getStyleClass().add("button");
        HBox buttons = new HBox();
        buttons.getStyleClass().add("input");
        buttons.getChildren().addAll(submit, reset);
        return buttons;
    }
    public static HBox getfloatInput(final HashMap<String, String> data){
        HBox input = new HBox();
        float defaultVal;
        input.getStyleClass().add("input");
        Text label = new Text(data.get("label"));
        final TextField textField = new TextField();
        try {
           defaultVal = Float.parseFloat(data.get("default"));
        }
       catch (NumberFormatException ex) {
           defaultVal = 0;
       }
        textField.textProperty().addListener(new ChangeListener<String>() {
            @Override public void changed(ObservableValue<? extends String> observable, String oldValue, String newValue) {
                try {
                    float min, max;
                    try{
                        min = Float.parseFloat(data.get("min"));
                        max = Float.parseFloat(data.get("max"));
                    }catch(NumberFormatException ex){
                        min = 0;
                        max = Float.MAX_VALUE;
                    }
                    if(!newValue.isEmpty()){
                        float value = Float.parseFloat(newValue);
                        if(min>value){
                            textField.setText(min+"");
                        }else if(max< value){
                            textField.setText(max+"");
                        }
                        
                    }
               }
               catch (NumberFormatException ex) {
                    textField.setText(oldValue);
               }
            }
        });
        textField.setText(defaultVal+"");
        input.getChildren().addAll(label, textField);
        return input;
    }
    
    public static HBox getintegerInput(final HashMap<String, String> data){
        HBox input = new HBox();
        int defaultVal;
        input.getStyleClass().add("input");
        Text label = new Text(data.get("label"));
        final TextField textField = new TextField();
        try {
           defaultVal = Integer.parseInt(data.get("default"));
        }
       catch (NumberFormatException ex) {
           defaultVal = 0;
       }
        textField.textProperty().addListener(new ChangeListener<String>() {
            @Override public void changed(ObservableValue<? extends String> observable, String oldValue, String newValue) {
                try {
                    int min, max;
                    try{
                        min = Integer.parseInt(data.get("min"));
                        max = Integer.parseInt(data.get("max"));
                    }catch(NumberFormatException ex){
                        min = 0;
                        max = Integer.MAX_VALUE;
                    }
                    if(!newValue.isEmpty()){
                        int value = Integer.parseInt(newValue);
                        if(min>value){
                            textField.setText(min+"");
                        }else if(max< value){
                            textField.setText(max+"");
                        }
                        
                    }
               }
               catch (NumberFormatException ex) {
                    textField.setText(oldValue);
               }
            }
        });
        textField.setText(defaultVal+"");
        input.getChildren().addAll(label, textField);
        return input;
    }
    
    public static HBox getcheckboxInput(final HashMap<String, String> data){
        HBox input = new HBox();
        Boolean defaultVal;
        input.getStyleClass().add("input");
        Text label = new Text(data.get("label"));
        final CheckBox checkBox = new CheckBox();
        try {
           defaultVal = Boolean.parseBoolean(data.get("checked"));
        }
       catch (NumberFormatException ex) {
           defaultVal = true;
       }
 
        checkBox.setSelected(defaultVal);
        input.getChildren().addAll(label, checkBox);
        return input;
    }
    
    public static HBox getInput(HashMap<String, String> data){
        HBox input = null;
        switch(data.get("type")){
        case "integer":
            input = getintegerInput(data);
            break;
        case "float":
            input = getfloatInput(data);
            break;
        case "checkbox":
            input = getcheckboxInput(data);
            break;
        }
        return input;
    }
}
