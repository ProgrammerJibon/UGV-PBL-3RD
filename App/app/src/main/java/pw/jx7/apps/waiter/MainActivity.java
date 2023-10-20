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

import androidx.annotation.Nullable;
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
    protected EditText student_password, student_phone;
    protected String str_student_phone, str_student_password;
    protected RelativeLayout progressCircular;
    protected Button connectBtn;
    protected Activity activity;
    protected CustomTools customTools;
    protected int SIGNUP_REQUEST_CODE = 1101;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        activity = this;
        setContentView(R.layout.activity_main);
        // find by id
        student_phone = activity.findViewById(R.id.student_phone);
        student_password = activity.findViewById(R.id.student_password);
        connectBtn = activity.findViewById(R.id.connectBtn);
        customTools = new CustomTools(activity);
        progressCircular = activity.findViewById(R.id.progressCircular);
        // set onclick listener
        connectBtn.setOnClickListener(v -> {
            if (student_phone.length() == 10){
                if (student_password.length() >= 4){
                    str_student_password = String.valueOf(student_password.getText());
                    str_student_phone = String.valueOf(student_phone.getText());
                    connectToNetwork();
                }else{
                    customTools.toast("Password must be minimum 4 characters!");
                    student_password.findFocus();
                }
            }else{
                customTools.toast("Please enter correct mobile number!");
                student_password.findFocus();
            }
        });


        str_student_password = customTools.setPref("student_password", null);
        str_student_phone = customTools.setPref("student_phone", null);
        if (!str_student_password.equals("") && !str_student_phone.equals("")){
            student_password.setText(str_student_password);
            student_phone.setText(str_student_phone);
            connectToNetwork();
        }
    }
    protected  void connectToNetwork(){
        connectToNetwork("", "");
    }

    protected void connectToNetwork(String studentId, String studentName){
        progressCircular.setVisibility(View.VISIBLE);
        Map<String , String > stringMap = new HashMap<>();
        stringMap.put("student_phone", str_student_phone);
        stringMap.put("student_password", str_student_password);
        stringMap.put("studentId", studentId);
        stringMap.put("studentName", studentName);
        Internet3 connectToServer = new Internet3(activity, CustomTools.url("json/app"), stringMap, (code, result) -> {
            progressCircular.setVisibility(View.GONE);
            try {
                if (code == 200) {
                    if (result.has("connectionResult")) {
                        if(result.has("new_student")){
                            customTools.setPref("student_password", str_student_password);
                            customTools.setPref("student_phone", str_student_phone);
                            Intent intent = new Intent(activity, SignupActivity.class);
                            activity.startActivityForResult(intent, SIGNUP_REQUEST_CODE);
                        }else if (result.getBoolean("connectionResult")){
                            student_password.clearFocus();
                            progressCircular.setVisibility(View.VISIBLE);

                            customTools.setPref("student_password", str_student_password);
                            customTools.setPref("student_phone", str_student_phone);

                            customTools.toast("Welcome back "+(result.has("student_name")?result.getString("student_name"):"!"));
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

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if(requestCode == SIGNUP_REQUEST_CODE && resultCode == RESULT_OK){
            try {
                assert data != null;
                connectToNetwork(data.getStringExtra("login_student_id"), data.getStringExtra("login_student_name"));
            }catch (Exception e){
                logE(e.getMessage());
            }
        }
    }

    public static String getAcquiredCode(String str){
        return str.replaceAll("[^0-9]", "");
    }


}