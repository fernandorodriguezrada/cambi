package com.cambi.app;

import android.os.Build;
import android.os.Bundle;
import android.view.Display;
import android.view.Window;
import android.view.WindowManager;
import com.getcapacitor.BridgeActivity;

public class MainActivity extends BridgeActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setHighRefreshRate();
    }

    @Override
    public void onResume() {
        super.onResume();
        setHighRefreshRate();
    }

    private void setHighRefreshRate() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            try {
                Window window = getWindow();
                if (window == null) return;
                WindowManager.LayoutParams params = window.getAttributes();

                Display display;
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
                    display = getDisplay();
                } else {
                    display = getWindowManager().getDefaultDisplay();
                }

                if (display != null) {
                    Display.Mode[] modes = display.getSupportedModes();
                    Display.Mode currentMode = display.getMode();

                    int bestModeId = -1;
                    float maxRefreshRate = 60.0f;

                    for (Display.Mode mode : modes) {
                        // Coincidir resolución física actual para no alterar el tamaño o DPI de pantalla
                        if (mode.getPhysicalWidth() == currentMode.getPhysicalWidth() &&
                            mode.getPhysicalHeight() == currentMode.getPhysicalHeight()) {
                            if (mode.getRefreshRate() > maxRefreshRate) {
                                maxRefreshRate = mode.getRefreshRate();
                                bestModeId = mode.getModeId();
                            }
                        }
                    }

                    if (bestModeId != -1) {
                        params.preferredDisplayModeId = bestModeId;
                    }
                    params.preferredRefreshRate = maxRefreshRate;

                    window.setAttributes(params);
                }
            } catch (Exception ignored) {
            }
        }
    }
}
