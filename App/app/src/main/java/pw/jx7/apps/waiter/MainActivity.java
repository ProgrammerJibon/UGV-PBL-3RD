package pw.jx7.apps.waiter;

import static pw.jx7.apps.waiter.tools.CustomTools.logE;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RelativeLayout;

import androidx.appcompat.app.AppCompatActivity;

import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import java.util.Timer;
import java.util.TimerTask;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import pw.jx7.apps.waiter.tools.CustomTools;
import pw.jx7.apps.waiter.tools.Internet2;
import pw.jx7.apps.waiter.tools.Internet3;

public class MainActivity extends AppCompatActivity {
    protected EditText studentCode, studentID;
    protected String str_student_id, str_student_code;
    protected RelativeLayout progressCircular;
    protected Button connectBtn;
    protected Activity activity;
    protected CustomTools customTools;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        activity = this;
        setContentView(R.layout.activity_main);
        // find by id
        studentID = activity.findViewById(R.id.studentID);
        studentCode = activity.findViewById(R.id.studentCode);
        connectBtn = activity.findViewById(R.id.connectBtn);
        customTools = new CustomTools(activity);
        progressCircular = activity.findViewById(R.id.progressCircular);
        // set onclick listener
        connectBtn.setOnClickListener(v -> {
            String code = getAcquiredCode(String.valueOf(studentCode.getText()));
            if (code.length() == 8){
                str_student_code = String.valueOf(studentCode.getText());
                str_student_id = String.valueOf(studentID.getText());
                connectToNetwork();
            }else{
                customTools.toast("Incorrect Code!");
                studentCode.findFocus();
            }
        });


        str_student_code = customTools.setPref("studentCode", null);
        str_student_id = customTools.setPref("studentId", null);
        if (!str_student_code.equals("") && !str_student_id.equals("")){
            studentCode.setText(str_student_code);
            studentID.setText(str_student_id);
            connectToNetwork();
        }
    }

    protected void connectToNetwork(){
        progressCircular.setVisibility(View.VISIBLE);
        Map<String , String > stringMap = new HashMap<>();
        stringMap.put("student_id", str_student_id);
        stringMap.put("student_code", str_student_code);
        Internet3 connectToServer = new Internet3(activity, CustomTools.url("json/app"), stringMap, (code, result) -> {
            progressCircular.setVisibility(View.GONE);
            try {
                if (code == 200) {
                    if (result.has("connectionResult")) {
                        if (result.getBoolean("connectionResult")){
                            studentCode.clearFocus();
                            progressCircular.setVisibility(View.VISIBLE);

                            customTools.setPref("studentCode", str_student_code);
                            customTools.setPref("studentId", str_student_id);

                            customTools.toast("Welcome back "+(result.has("connectionUsername")?result.getString("connectionUsername"):"!"));
                            new Timer().schedule(new TimerTask() {
                                @Override
                                public void run() {
                                    Intent intent = new Intent(activity, TableFullScreenView.class);
                                    activity.startActivity(intent);
                                    activity.finish();
                                }
                            }, 1000);
                        }else{
                            customTools.toast("Incorrect Code!", R.drawable.baseline_portable_wifi_off_24, R.color.gray);
                        }
                    }

                } else {
                    customTools.toast("Server off!", R.drawable.baseline_portable_wifi_off_24, R.color.orange);
                }
            }catch (Exception e){
                customTools.toast("Something went wrong!\n"+e.getMessage());
            }
        });
        connectToServer.executeOnExecutor(AsyncTask.THREAD_POOL_EXECUTOR);
    }

    public static String getAcquiredCode(String str){
        return str.replaceAll("[^0-9]", "");
    }


}