package pw.jx7.apps.waiter;

import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AppCompatActivity;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Intent;
import android.content.pm.ApplicationInfo;
import android.os.Build;
import android.os.Bundle;
import android.text.Html;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.gms.ads.AdRequest;
import com.google.android.gms.ads.AdSize;
import com.google.android.gms.ads.AdView;
import com.google.android.gms.ads.MobileAds;
import com.google.firebase.FirebaseApp;
import com.google.firebase.FirebaseException;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.PhoneAuthCredential;
import com.google.firebase.auth.PhoneAuthOptions;
import com.google.firebase.auth.PhoneAuthProvider;

import java.util.HashMap;
import java.util.Map;
import java.util.Timer;
import java.util.TimerTask;
import java.util.concurrent.TimeUnit;

import pw.jx7.apps.waiter.tools.CustomTools;
import pw.jx7.apps.waiter.tools.Internet3;

public class SignupActivity extends AppCompatActivity {
    Activity activity;

    String phoneNumber, phoneNumberOnly;
    Long timeoutSeconds = 60L;
    String verificationCode;
    PhoneAuthProvider.ForceResendingToken resendingToken;

    EditText otpInput, login_student_name, login_student_id;
    Button nextBtn;
    ProgressBar progressBar;
    FirebaseAuth mAuth;
    TextView resendOtpTextView, change_phone_number;
    private Boolean isResend = false;
    private CustomTools customTools;

    @RequiresApi(api = Build.VERSION_CODES.N)
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        activity = this;
        customTools = new CustomTools(activity);
        setContentView(R.layout.activity_signup);

        otpInput = findViewById(R.id.login_otp);
        login_student_name = findViewById(R.id.login_student_name);
        login_student_id = findViewById(R.id.login_student_id);
        nextBtn = findViewById(R.id.login_next_btn);
        progressBar = findViewById(R.id.login_progress_bar);
        resendOtpTextView = findViewById(R.id.resend_otp_textview);
        change_phone_number = findViewById(R.id.change_phone_number);

        FirebaseApp.initializeApp(this);

        mAuth = FirebaseAuth.getInstance();

        phoneNumber = "+880" + customTools.getPref("student_phone");

        change_phone_number.setText(Html.fromHtml("Your number: "+phoneNumber));

        if (!customTools.getPref("otp_send" + phoneNumber).equalsIgnoreCase("1")) {
            sendOtp(phoneNumber);
        } else {
            if ((0 != (getApplicationInfo().flags & ApplicationInfo.FLAG_DEBUGGABLE))) {
                timeoutSeconds = 3L;
            } else {
                timeoutSeconds = 30L;
            }
            startResendTimer();
            customTools.toast("Try resending OTP after " + timeoutSeconds + "seconds");
            nextBtn.setVisibility(View.GONE);
        }



        nextBtn.setOnClickListener(v -> {
            if(login_student_id.length() < 8){
                customTools.toast("Enter your student id correctly.");
            }else if(login_student_name.length() < 8){
                customTools.toast("Enter your name correctly.");
            }else{
                String enteredOtp = otpInput.getText().toString();
                PhoneAuthCredential credential = PhoneAuthProvider.getCredential(verificationCode, enteredOtp);
                signIn(credential);
            }
        });

        resendOtpTextView.setOnClickListener((v) -> {
            sendOtp(phoneNumber);
        });

        //ads area
        MobileAds.initialize(this, initializationStatus -> {});
        LinearLayout adLayout = activity.findViewById(R.id.adLayout);
        AdView mAdView = new AdView(this);
        if (( 0 != ( getApplicationInfo().flags & ApplicationInfo.FLAG_DEBUGGABLE ) )){
            mAdView.setAdUnitId("ca-app-pub-3940256099942544/6300978111");
        }else {
            mAdView.setAdUnitId("ca-app-pub-6695709429891253/7579520506");
        }
        mAdView.setAdSize(AdSize.SMART_BANNER);
        adLayout.addView(mAdView);
        AdRequest adRequest = new AdRequest.Builder().build();
        mAdView.loadAd(adRequest);
    }
    void sendOtp(String phoneNumber) {
        setInProgress(true);
        PhoneAuthOptions.Builder builder =
                PhoneAuthOptions.newBuilder(mAuth)
                        .setPhoneNumber(phoneNumber)
                        .setTimeout(timeoutSeconds, TimeUnit.SECONDS)
                        .setActivity(this)
                        .setCallbacks(new PhoneAuthProvider.OnVerificationStateChangedCallbacks() {
                            @Override
                            public void onVerificationCompleted(@NonNull PhoneAuthCredential phoneAuthCredential) {
                                signIn(phoneAuthCredential);
                                setInProgress(false);
                            }

                            @Override
                            public void onVerificationFailed(@NonNull FirebaseException e) {
                                Toast.makeText(activity, "OTP verification failed", Toast.LENGTH_LONG).show();
                                setInProgress(false);
                                resendOtpTextView.setText("Send OTP");
                            }

                            @Override
                            public void onCodeSent(@NonNull String s, @NonNull PhoneAuthProvider.ForceResendingToken forceResendingToken) {
                                super.onCodeSent(s, forceResendingToken);
                                verificationCode = s;
                                resendingToken = forceResendingToken;
                                Toast.makeText(activity, "OTP sent to " + phoneNumber, Toast.LENGTH_LONG).show();
                                startResendTimer();
                                setInProgress(false);
                                customTools.setPref("otp_send" + phoneNumber, "1");
                                isResend = true;
                            }
                        });
        if(isResend){
            PhoneAuthProvider.verifyPhoneNumber(builder.setForceResendingToken(resendingToken).build());
        }else{
            PhoneAuthProvider.verifyPhoneNumber(builder.build());
        }

    }

    void setInProgress(boolean inProgress){
        if(inProgress){
            progressBar.setVisibility(View.VISIBLE);
            nextBtn.setVisibility(View.GONE);
            resendOtpTextView.setVisibility(View.GONE);
        }else{
            progressBar.setVisibility(View.GONE);
            nextBtn.setVisibility(View.VISIBLE);
            resendOtpTextView.setVisibility(View.VISIBLE);
        }
    }

    void signIn(PhoneAuthCredential phoneAuthCredential) {
        //login and go to next activity
        setInProgress(true);
        mAuth.signInWithCredential(phoneAuthCredential).addOnCompleteListener(task -> {
            setInProgress(false);
            if (task.isSuccessful()) {
                customTools.toast("OTP verified successfully");
                Intent resultIntent = new Intent();
                resultIntent.putExtra("login_student_id", String.valueOf(login_student_id.getText()));
                resultIntent.putExtra("login_student_name", String.valueOf(login_student_name.getText()));
                setResult(RESULT_OK, resultIntent);
                finish();
            } else {
                customTools.toast("OTP verification failed");
            }
        });


    }

    void startResendTimer(){
        resendOtpTextView.setEnabled(false);
        Timer timer = new Timer();
        timer.scheduleAtFixedRate(new TimerTask() {
            @Override
            public void run() {
                timeoutSeconds--;
                resendOtpTextView.setText("Resend OTP in "+timeoutSeconds +" seconds");
                if(timeoutSeconds<=0){
                    timeoutSeconds = 60L;
                    timer.cancel();
                    runOnUiThread(() -> {
                        resendOtpTextView.setText("Resend OTP");
                        resendOtpTextView.setEnabled(true);
                    });
                }
            }
        },0,1000);
    }
}