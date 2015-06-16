package states;

public class ApplicationStates {
    private Boolean showMenu = true;
    private Boolean helpOn = true;
    public static final double MENU_BOX_WIDTH = 200;
    public void setShowMenu(Boolean v){
        showMenu = v;
    }
    
    public Boolean getShowMenu(){
        return showMenu;
    }

    
    public void setHelpOn(Boolean v){
        helpOn = v;
    }
    
    public Boolean isHelpOn(){
        return helpOn;
    }
}
