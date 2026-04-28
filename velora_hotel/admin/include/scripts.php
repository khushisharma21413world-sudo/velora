<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-close alert after 4s
setTimeout(()=>{
  document.querySelectorAll('.custom-alert').forEach(a=>{
    const bsAlert = bootstrap.Alert.getInstance(a);
    if(bsAlert) bsAlert.close(); else a.remove();
  });
},4000);
</script>
