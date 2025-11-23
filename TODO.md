Dashboard Folder Refactor - Remove JSON Responses & Implement Classic Form Submission

Tasks:  

1. Update Booking (update_booking.php)  
   - Remove JSON header and outputs  
   - Use POST form data instead of JSON input  
   - Add session success/error messages  
   - Redirect to dashboard.php after processing  

2. Toggle Room (toggle_room.php)  
   - Remove JSON header and outputs  
   - Use POST form data  
   - Add session success/error messages  
   - Redirect to dashboard.php  

3. Cancel Booking (cancel_booking.php)  
   - Remove JSON header and outputs  
   - Use POST form data  
   - Add session messages  
   - Redirect to dashboard.php  

4. Delete Booking (delete_booking.php)  
   - Remove JSON outputs  
   - Change JSON input to POST/GET params  
   - Add session messages  
   - Redirect to dashboard.php  

Next Steps:  
- Edit code for each file in order listed  
- Test functionality with classic form submissions after changes  
- Verify session messages display expected success/error feedback
