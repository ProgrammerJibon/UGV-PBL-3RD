package pw.jx7.apps.waiter.tools;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.os.Build;
import android.os.Vibrator;
import android.provider.MediaStore;
import android.telephony.TelephonyManager;
import android.text.InputFilter;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.PopupWindow;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatDelegate;
import androidx.core.content.ContextCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.dialog.MaterialAlertDialogBuilder;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.List;
import java.util.Locale;
import java.util.Objects;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import pw.jx7.apps.waiter.R;

public class CustomTools {
    public static final String TITLE = "UGV Cafe";
    public static String URL = "https://ugv-pbl-3.jibon.pro";
    public static String TAG = "errnos";
    protected SharedPreferences preferences;
    public static String CURRENCY_SIGN = "৳";
    protected SharedPreferences.Editor preferencesEditor;
    protected Activity activity;

    public CustomTools(Activity activity) {
        this.activity = activity;
        this.preferences = activity.getSharedPreferences("app", Context.MODE_PRIVATE);
    }

    public static void DoNothing() {
    }

    public static String url(String path) {
        return URL + "/" + path;
    }

    public static void logE(Object message) {
        Log.e(TAG, String.valueOf(message));
    }

    public static void setEditTextMaxLength(EditText editText, Integer maxLength) {
        InputFilter[] filters = new InputFilter[1];
        filters[0] = new InputFilter.LengthFilter(maxLength);
        editText.setFilters(filters);
    }



    public static boolean isIPAddress(String s) {
        String patternString = "^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\." +
                "([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\." +
                "([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\." +
                "([01]?\\d\\d?|2[0-4]\\d|25[0-5])$";
        Pattern pattern = Pattern.compile(patternString);
        Matcher matcher = pattern.matcher(s);
        return matcher.matches();
    }


    public static Bitmap scaleDownBitmap(Bitmap bitmap, int newWidth) {
        int originalWidth = bitmap.getWidth();
        int originalHeight = bitmap.getHeight();
        int newHeight = (int) ((float) originalHeight / ((float) originalWidth / (float) newWidth));
        return Bitmap.createScaledBitmap(bitmap, newWidth, newHeight, false);
    }


    public static Bitmap BitmapFromFilePicker(Activity activity, Intent data) {
        try {
            Uri uri = data.getData();
            return MediaStore.Images.Media.getBitmap(activity.getContentResolver(), uri);
        } catch (Exception e) {
            logE(e);
            return null;
        }
    }


    public String getPref(String id) {
        return setPref(id, null);
    }

    public String setPref(String id, String data) {
        String result = "";
        id = id;
        if (data != null) {
            preferencesEditor = preferences.edit();
            preferencesEditor.putString(id, data);
            preferencesEditor.commit();
        }
        if (!preferences.getString(id, "").equals("")) {
            result = preferences.getString(id, "");
            if (Objects.equals(result, "")) {
                result = "";
            }
        } else {
            preferencesEditor = preferences.edit();
            preferencesEditor.putString(id, "");
            preferencesEditor.commit();
        }
        return result;
    }

    public boolean toast(String text, Integer drawable, int color) {
        try {
            View view = activity.getLayoutInflater().inflate(R.layout.toast, activity.findViewById(R.id.Custom_toast), false);
            ((TextView) view.findViewById(R.id.Custom_toast_text)).setText(text);
            if (drawable != null) {
                ((ImageView) view.findViewById(R.id.Custom_toast_icon)).setImageResource(drawable);
                ((ImageView) view.findViewById(R.id.Custom_toast_icon)).setColorFilter(ContextCompat.getColor(activity, color), PorterDuff.Mode.SRC_IN);
            }
            Toast toast = new Toast(activity.getApplicationContext());
            toast.setDuration(Toast.LENGTH_LONG);
            toast.setView(view);
            toast.show();
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
                new CustomTools(activity).vibrate(1500);
            }
            return true;
        } catch (Exception e) {
            Log.e("errnos_ctool_a", "Custom Toast Problem: " + e);
            return false;
        }
    }

    public boolean toast(String text, Integer drawable) {
        return toast(text, drawable, Color.GRAY);
    }

    public boolean toast(String text) {
        return toast(text, null, 0);
    }

    public boolean toast(Integer text) {
        return toast(String.valueOf(text), null);
    }

    public boolean vibrate(int milliseconds) {
        try {
            Vibrator vibrator = (Vibrator) activity.getSystemService(Context.VIBRATOR_SERVICE);
            vibrator.vibrate(milliseconds);
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
                vibrator.vibrate(Vibrator.VIBRATION_EFFECT_SUPPORT_YES);
            }
            return true;
        } catch (Exception e) {
            Log.e("errnos_ctool_b", "Vibrate Problem: " + e);
            return false;
        }
    }

    public void alert(String title, String messages, int icon, int color) {
        AlertDialog.Builder builder = new AlertDialog.Builder(activity);
        Drawable drawable = activity.getResources().getDrawable(icon);
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            drawable.setTint(color);
        }
        builder.setTitle(title)
                .setMessage(messages)
                .setIcon(drawable)
                .setPositiveButton("OK", (dialog, which) -> {
                    dialog.cancel();
                })
                .setCancelable(true);
        builder.create().show();
    }

    public void alert(String title, String messages, int icon) {
        alert(title, messages, icon, Color.GRAY);
    }

    public void alert(String title, String messages) {
        alert(title, messages, R.drawable.baseline_notifications_none_24, Color.GRAY);
    }

    public void setDefDarkMode() {
        Boolean darkMode = getPref("dark_mode").equalsIgnoreCase(String.valueOf(AppCompatDelegate.MODE_NIGHT_YES));
        int nightMode = darkMode ? AppCompatDelegate.MODE_NIGHT_YES : AppCompatDelegate.MODE_NIGHT_NO;
        AppCompatDelegate.setDefaultNightMode(nightMode);
    }


}