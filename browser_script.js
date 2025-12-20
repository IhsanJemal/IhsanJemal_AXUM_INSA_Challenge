
// Paste this in browser console (F12)
document.cookie = "SID=admin_demo; path=/; domain=localhost";
console.log("Admin cookie set: SID=admin_demo");

// Auto-redirect to admin panel
setTimeout(function() {
    window.location.href = "http://localhost:8080/admin.php";
}, 1000);
