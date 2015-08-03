package airavata;

import org.apache.airavata.model.error.AiravataClientConnectException;
import org.apache.thrift.TException;
import org.json.simple.JSONObject;

public class LaunchExperiment {
    public static void main(String[] args) {
        if (args.length == 2) {
             String appId = args[0];
             String json = args[1];
             JSONObject error = new JSONObject();
            try {
                ExperimentUtils exp = new ExperimentUtils();
                String projId = exp.CreateProject("test");
                String expId=exp.CreateExperiment("exp5", projId, appId,json);
                exp.launchExperiment(expId);
                System.out.println(exp.getOutput(expId));
            } catch (TException e) {
                error.put("error", e.getMessage());
                System.out.println(error);
                e.printStackTrace();
            } catch (AiravataClientConnectException e) {
                error.put("error", e.getMessage());
                System.out.println(error);
            }
            catch (InterruptedException e) {
                error.put("error", e.getMessage());
                System.out.println(error);
            }
        } else {
           System.out.println("{\"error\":\"insufficient arguments\"}");
        }

    }
}
