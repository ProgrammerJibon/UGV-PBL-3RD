package pw.jx7.apps.waiter;

import android.content.Context;
import android.os.AsyncTask;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class FileHelper {

    public static void downloadAndSaveFile(Context context, String url, String fileName, OnFileDownloadListener listener) {
        new DownloadTask(context, fileName, listener).execute(url);
    }

    public interface OnFileDownloadListener {
        void onFileDownloaded(String filePath);

        void onFileDownloadFailed();
    }

    private static class DownloadTask extends AsyncTask<String, Void, String> {
        private Context context;
        private String fileName;
        private OnFileDownloadListener listener;

        public DownloadTask(Context context, String fileName, OnFileDownloadListener listener) {
            this.context = context;
            this.fileName = fileName;
            this.listener = listener;
        }

        @Override
        protected String doInBackground(String... urls) {
            String url = urls[0];
            File file = new File(context.getFilesDir(), fileName);

            InputStream inputStream = null;
            FileOutputStream fos = null;
            try {
                URL fileUrl = new URL(url);
                HttpURLConnection connection = (HttpURLConnection) fileUrl.openConnection();
                connection.setRequestMethod("GET");
                connection.connect();

                inputStream = connection.getInputStream();
                fos = context.openFileOutput(fileName, Context.MODE_PRIVATE);

                byte[] buffer = new byte[4096];
                int bytesRead;
                while ((bytesRead = inputStream.read(buffer)) != -1) {
                    fos.write(buffer, 0, bytesRead);
                }

                return file.getAbsolutePath();
            } catch (IOException e) {
                e.printStackTrace();
            } finally {
                if (inputStream != null) {
                    try {
                        inputStream.close();
                    } catch (IOException e) {
                        e.printStackTrace();
                    }
                }
                if (fos != null) {
                    try {
                        fos.close();
                    } catch (IOException e) {
                        e.printStackTrace();
                    }
                }
            }
            return null;
        }

        @Override
        protected void onPostExecute(String filePath) {
            if (filePath != null) {
                // File was successfully downloaded and saved.
                if (listener != null) {
                    listener.onFileDownloaded(filePath);
                }
            } else {
                // File download or saving failed. Handle the error.
                if (listener != null) {
                    listener.onFileDownloadFailed();
                }
            }
        }
    }
}
