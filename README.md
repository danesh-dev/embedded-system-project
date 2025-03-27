# GPS Tracking and Emergency Alert System

## Project Overview
A comprehensive GPS tracking device with emergency features designed for personal safety, suitable for vehicles or personal use.

## Core Features
1. Real-time GPS Tracking
   - Continuous location monitoring
   - Send location data to web API
   - Use NEO-6M GPS module

2. Emergency Alert System
   - Dedicated emergency button
   - Automatic accident detection
   - SMS alerts to predefined contacts
   - Uses SIM800L GSM module

3. Power Management
   - High-capacity 18650 Li-ion battery (4000mAh+)
   - Battery level indication
   - Safe charging via TP4056 module
   - Power stabilization with AMS1117 regulator

4. User Interface
   - On/Off switch
   - Emergency button
   - Accident sensor deactivation button
   - LED indicators for:
     * Power status
     * GSM signal strength
     * GPS lock status
     * Battery level

## Technical Specifications
### Microcontroller
- ATmega328P-PU
- Operating Voltage: 5V
- Clock Speed: 16 MHz

### Sensors and Modules
1. GPS Module: NEO-6M
   - Interface: UART
   - Voltage: 3.3V

2. GSM Module: SIM800L
   - Interface: UART
   - Voltage: 3.7-4.2V

3. Accelerometer: ADXL345
   - Interface: I2C
   - Voltage: 3.3V
   - Used for crash/accident detection

### Power System
1. Battery: 18650 Li-ion
   - Capacity: 4000mAh
   - Voltage: 3.7V nominal

2. Charging Circuit: TP4056
   - Supports safe Li-ion charging
   - Includes over-charge and over-discharge protection

3. Voltage Regulator: AMS1117
   - Input: 3.7-5V
   - Output: 3.3V and 5V

### Connectivity
- Web API Backend: Laravel (PHP)
- Database: MySQL
- Frontend: HTML/CSS/JavaScript

### Firmware
- Development Environment: Arduino IDE
- Programming Language: C/C++
