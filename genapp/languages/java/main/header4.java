case "__menu:label__":
                setMenuImage("__menu:icon__");
                MenuBox.ToggleMenuBox(scene.lookup("#Menu_Box"));
                content.getChildren().set(0, getModuleButtons("__menu:label__"));
                content.getChildren().set(1, new VBox());
                break;