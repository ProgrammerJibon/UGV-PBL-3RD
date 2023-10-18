package pw.jx7.apps.waiter;


import static pw.jx7.apps.waiter.tools.CustomTools.CURRENCY_SIGN;
import static pw.jx7.apps.waiter.tools.CustomTools.logE;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.goodcom.gcprinter.GcPrinterUtils;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.net.URLEncoder;
import java.util.HashMap;
import java.util.Map;
import java.util.Timer;
import java.util.TimerTask;

import pw.jx7.apps.waiter.Adapters.OrderedItemsAdapter;
import pw.jx7.apps.waiter.tools.CustomTools;
import pw.jx7.apps.waiter.tools.Internet2;
import pw.jx7.apps.waiter.tools.Internet3;

public class TableFullScreenView extends AppCompatActivity {
    public Activity activity;
    public Button openTableButton, closeTableButton, addItemTableButton, printItemsButton;
    public String student_id, student_code, order_id,  order_time;
    protected TextView pageTitle, orderIDTextShow, bookingTime, itemOnlyTotal;
    private CustomTools customTools;
    protected RelativeLayout bookTableView, openTableView;
    protected RecyclerView orderedItemsRecyclerView;
    public OrderedItemsAdapter orderedItemsAdapter;
    public JSONArray orderedItem = new JSONArray();


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        activity = this;
        customTools = new CustomTools(activity);
        student_code = customTools.setPref("studentCode", null);
        student_id = customTools.setPref("studentId", null);
        setContentView(R.layout.activity_table_full_screen_view);

        // find view by id
        bookTableView = activity.findViewById(R.id.bookTableView);
        openTableView = activity.findViewById(R.id.openTableView);
        pageTitle = activity.findViewById(R.id.pageTitle);
        openTableButton = activity.findViewById(R.id.openTableButton);
        itemOnlyTotal = activity.findViewById(R.id.itemOnlyTotal);

        orderIDTextShow = activity.findViewById(R.id.orderIDTextShow);
        bookingTime = activity.findViewById(R.id.bookingTime);
        orderedItemsRecyclerView = activity.findViewById(R.id.orderedItemsRecyclerView);
        closeTableButton = activity.findViewById(R.id.closeTableButton);
        addItemTableButton = activity.findViewById(R.id.addItemTableButton);
        printItemsButton = activity.findViewById(R.id.printItemsButton);


        addItemTableButton.setOnClickListener(v -> {
            Intent intent = new Intent(activity, AddItemToOrderListGroupsActivity.class);
            intent.putExtra("order_id", order_id);
            activity.startActivity(intent);
        });



        closeTableButton.setOnClickListener(v -> closeTable());


        syncTableStatus();

        openTableButton.setOnClickListener(v -> {
            openTableButtonClicked();
        });

        load_ordered_items();
        orderedItemsRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        orderedItemsAdapter = new OrderedItemsAdapter(activity, orderedItem);
        orderedItemsRecyclerView.setAdapter(orderedItemsAdapter);
    }


    private void syncTableStatus(){
        Map<String, String> stringMap = new HashMap<>();
        stringMap.put("student_id", student_id);
        stringMap.put("student_code", student_code);
        stringMap.put("syncTable", "1");
        Internet3 internet3 = new Internet3(activity, CustomTools.url("json/app"), stringMap, ((code, result) -> {
            try {
                if (result.has("book_table")) {
                    if (result.getBoolean("book_table")){
                        order_id = result.getString("order_id");
                        order_time = result.getString("time");

                        bookTableView.setVisibility(View.GONE);
                        openTableView.setVisibility(View.VISIBLE);

                        orderIDTextShow.setText("Booking ID: "+order_id);
                        bookingTime.setText("Sit Time: "+order_time);
                        load_ordered_items();
                    }else{
                        customTools.toast("Table not booked yet!");
                    }
                }
            }catch (Exception error){
                logE(error);
            }
        }));
        internet3.executeOnExecutor(AsyncTask.SERIAL_EXECUTOR);
    }


    private void openTableButtonClicked() {
        Map<String, String> stringMap = new HashMap<>();
        stringMap.put("student_id", student_id);
        stringMap.put("student_code", student_code);
        stringMap.put("book_table", "1");
        Internet3 internet3 = new Internet3(activity, CustomTools.url("json/app"), stringMap, ((code, result) -> {
            try {
                if (result.has("book_table")) {
                    if (result.getBoolean("book_table")){
                        order_id = result.getString("order_id");
                        order_time = result.getString("time");

                        bookTableView.setVisibility(View.GONE);
                        openTableView.setVisibility(View.VISIBLE);

                        orderIDTextShow.setText("Booking ID: "+order_id);
                        bookingTime.setText("Sit Time: "+order_time);
                        load_ordered_items();
                    }else{
                        customTools.toast("Something went wrong!");
                    }
                }
            }catch (Exception error){
                logE(error);
            }
        }));
        internet3.executeOnExecutor(AsyncTask.SERIAL_EXECUTOR);
    }

    public void closeTable(){
        closeTableButton.setClickable(false);
        closeTableButton.setAlpha(0.5F);
        addItemTableButton.setVisibility(View.GONE);
        printItemsButton.setVisibility(View.GONE);
        Map<String, String> stringMap = new HashMap<>();
        stringMap.put("student_id", student_id);
        stringMap.put("student_code", student_code);
        stringMap.put("tableClosed", order_id);
        Internet3 internet3 = new Internet3(activity, CustomTools.url("json/app"), stringMap, ((code, result) -> {
            try {
                if (result.has("tableClosed")) {
                    if (result.getBoolean("closeStatus")){
                        closeTableButton.setText("CLOSED");
                    }else{
                        closeTableButton.setClickable(true);
                        closeTableButton.setAlpha(1F);
                        addItemTableButton.setVisibility(View.VISIBLE);
                        printItemsButton.setVisibility(View.VISIBLE);
                        customTools.toast("Try again later...");
                    }
                }
            }catch (Exception e){
                Log.e("errnos", e.getMessage());
            }
        }));
        internet3.executeOnExecutor(AsyncTask.THREAD_POOL_EXECUTOR);
    }



    @Override
    protected void onResume() {
        super.onResume();
//        load_ordered_items();
    }

    public void load_ordered_items(){
        if (order_id != null){
            Map<String, String> stringMap = new HashMap<>();
            stringMap.put("student_id", student_id);
            stringMap.put("student_code", student_code);
            stringMap.put("ordered_items", order_id);
            Internet3 internet3 = new Internet3(activity, CustomTools.url("json/app"), stringMap, ((code, result) -> {
                try {
                    new Timer().schedule(new TimerTask() {
                        @Override
                        public void run() {
                            load_ordered_items();
                        }
                    },1000);
                    if (result.has("ordered_items")) {
                        orderedItem = result.getJSONArray("ordered_items");
                        printItemsButton.setOnClickListener(v -> {
                            load_ordered_items();
                        });
                        if (orderedItem.length() > 0){
                            orderedItemsAdapter.updateData(orderedItem);
                            Float makeTotal = Float.parseFloat("0");
                            for (int i = 0; i < orderedItem.length(); i++) {
                                JSONObject item = orderedItem.getJSONObject(i);
                                Float thisItemTotal = Float.parseFloat(item.getString("price_then")) * Float.parseFloat(item.getString("item_quantity"));
                                makeTotal += thisItemTotal;
                            }
                            itemOnlyTotal.setText(CURRENCY_SIGN + String.format("%.2f", makeTotal));
                        }
                    }
                }catch (Exception e){
                    Log.e("errnos ", "\t" + e);
                }
            }));
            internet3.executeOnExecutor(AsyncTask.THREAD_POOL_EXECUTOR);
        }
    }
}
