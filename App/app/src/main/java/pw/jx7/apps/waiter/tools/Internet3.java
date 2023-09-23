package pw.jx7.apps.waiter.tools;

import android.app.Activity;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.os.Build;
import android.util.Log;
import android.webkit.CookieManager;


import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.ByteArrayOutputStream;
import java.io.DataOutputStream;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.util.List;
import java.util.Map;

import pw.jx7.apps.waiter.R;


public class Internet3 extends AsyncTask<Void, Void, JSONObject> {
    private final TaskListener taskListener;
    private final String boundary = "*****";
    private final String lineEnd = "\r\n";
    String twoHyphens = "--";
    private Activity context;
    private String url;
    private Integer code = 0;
    private Map<String, String> inputs = null;
    private Map<String, Bitmap> files = null;

    private String allLines = "";


    public Internet3(Activity context, String url, TaskListener listener) {
        this.context = context;
        this.url = url;
        this.taskListener = listener;
        this.inputs = null;
        this.files = null;
    }

    public Internet3(Activity context, String url, Map<String, String> inputs, TaskListener listener) {
        this.context = context;
        this.url = url;
        this.taskListener = listener;
        this.inputs = inputs;
        this.files = null;
    }

    public Internet3(Activity context, String url, Map<String, String> inputs, Map<String, Bitmap> files, TaskListener listener) {
        this.context = context;
        this.url = url;
        this.taskListener = listener;
        this.inputs = inputs;
        this.files = files;
    }

    @Override
    protected void onPostExecute(JSONObject result) {
        try {
            super.onPostExecute(result);
            if (this.taskListener != null) {
                this.taskListener.onFinished(code, result);
            }
        } catch (Exception e) {
            Log.e(CustomTools.TAG, e.toString());
        }
    }

    public void connect() {
        this.executeOnExecutor(Internet3.THREAD_POOL_EXECUTOR);
    }

    @Override
    protected JSONObject doInBackground(Void... params) {
        try {
            URL newLink = new URL(url);
            HttpURLConnection httpURLConnection = (HttpURLConnection) newLink.openConnection();
            // Fetch and set cookies in requests
            CookieManager cookieManager = CookieManager.getInstance();
            String cookie = cookieManager.getCookie(httpURLConnection.getURL().toString());
            if (cookie != null) {
                httpURLConnection.setRequestProperty("Cookie", cookie);
            }
            httpURLConnection.setRequestMethod("POST");

            String userAgent = "Device: Android " + Build.VERSION.RELEASE + ", Manufacturer: " + Build.MANUFACTURER + ", Model: " + Build.MODEL + ", Brand: " + Build.BRAND + ", App: " + context.getString(R.string.app_name) + " " ;//+ BuildConfig.VERSION_NAME + "";

            httpURLConnection.setRequestProperty("User-Agent", userAgent);
            httpURLConnection.setRequestProperty("Connection", "Keep-Alive");
            httpURLConnection.setRequestProperty("Content-Type", "multipart/form-data;boundary=" + boundary);
            httpURLConnection.setDoInput(true);
            httpURLConnection.setDoOutput(true);
            httpURLConnection.setUseCaches(false);
            httpURLConnection.connect();
            OutputStream outputStream = httpURLConnection.getOutputStream();
            DataOutputStream dataOutputStream = new DataOutputStream(outputStream);

            // add parameters
            if (inputs != null) {
                for (Map.Entry<String, String> entry : inputs.entrySet()) {
                    String key = entry.getKey();
                    String value = entry.getValue();
                    if (!key.equals("") && !value.equals("")) {
                        addFormField(key, value, dataOutputStream);
                    }
                }
            }

            // add images
            if (files != null) {
                for (Map.Entry<String, Bitmap> entry : files.entrySet()) {
                    String key = entry.getKey();
                    Bitmap value = entry.getValue();
                    if (!key.equals("") && value != null) {
                        addFilePart(key, value, dataOutputStream);
                    }
                }
            }

            dataOutputStream.writeBytes(twoHyphens + boundary + twoHyphens + lineEnd);
            dataOutputStream.flush();
            dataOutputStream.close();
            outputStream.close();
            this.code = httpURLConnection.getResponseCode();
            // Get cookies from responses and save into the cookie manager
            List<String> cookieList = httpURLConnection.getHeaderFields().get("Set-Cookie");
            if (cookieList != null) {
                for (String cookieTemp : cookieList) {
                    cookieManager.setCookie(httpURLConnection.getURL().toString(), cookieTemp);
                }
            }
            InputStream inputStream = httpURLConnection.getInputStream();
            BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(inputStream));
            StringBuilder stringBuilder = new StringBuilder();
            String line;
            while ((line = bufferedReader.readLine()) != null) {
                stringBuilder.append(line);
            }
            allLines = stringBuilder.toString();
            JSONObject jsonObject = new JSONObject(allLines);
            return jsonObject;
        } catch (Exception e) {
            Log.e(CustomTools.TAG, this.url + " - Internet3 error:" + e);
            Log.e(CustomTools.TAG, allLines);
            return null;
        }
    }

    private void addFormField(String fieldName, String fieldValue, OutputStream outputStream) {
        try {
            StringBuilder builder = new StringBuilder();
            builder.append(twoHyphens).append(boundary).append(lineEnd);
            builder.append("Content-Disposition: form-data; name=\"").append(fieldName).append("\"").append(lineEnd);
            builder.append(lineEnd);
            builder.append(fieldValue).append(lineEnd);

            outputStream.write(builder.toString().getBytes(StandardCharsets.UTF_8));
            outputStream.flush();
        } catch (Exception e) {
            Log.e(CustomTools.TAG, e.getMessage());
        }
    }

    private void addFilePart(String paramName, Bitmap bitmap, OutputStream outputStream) {
        try {
            String fileName = "image-" + Math.random() + ".png";
            String contentType = "image/png";

            // Create the file part header
            StringBuilder sb = new StringBuilder();
            sb.append(twoHyphens).append(boundary).append(lineEnd);
            sb.append("Content-Disposition: form-data; name=\"").append(paramName).append("\"; filename=\"").append(fileName).append("\"").append(lineEnd);
            sb.append("Content-Type: ").append(contentType).append(lineEnd);
            sb.append(lineEnd);
            outputStream.write(sb.toString().getBytes());

            // Write the bitmap data to the output stream
            ByteArrayOutputStream baos = new ByteArrayOutputStream();
            bitmap.compress(Bitmap.CompressFormat.PNG, 100, baos);
            byte[] imageData = baos.toByteArray();
            outputStream.write(imageData);

            // Add the closing boundary
            outputStream.write(lineEnd.getBytes());
            outputStream.write((twoHyphens + boundary + twoHyphens + lineEnd).getBytes());
        } catch (Exception e) {
            Log.e(CustomTools.TAG, e.getMessage());
        }
    }

    public interface TaskListener {
        void onFinished(Integer code, JSONObject result);
    }


}