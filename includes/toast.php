<?php
// Toast component
// You can set $id if you want multiple toasts on the page
$id = $id ?? 'projectToast';
?>

<!-- Toast container -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
  <div id="<?= $id ?>" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="<?= $id ?>Message">
        <!-- Message will be set dynamically -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script>
function showToast(message, type = "success", toastId = "<?= $id ?>") {
    const toastEl = document.getElementById(toastId);
    const toastMessage = document.getElementById(toastId + "Message");

    toastMessage.textContent = message;

    // Change toast color
    toastEl.classList.remove("bg-success", "bg-danger", "bg-warning", "bg-info");
    switch(type) {
        case "success": toastEl.classList.add("bg-success"); break;
        case "error": toastEl.classList.add("bg-danger"); break;
        case "warning": toastEl.classList.add("bg-warning"); break;
        case "info": toastEl.classList.add("bg-info"); break;
        default: toastEl.classList.add("bg-success"); break;
    }

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}
</script>
