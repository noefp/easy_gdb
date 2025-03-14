<?php include_once realpath("../../header.php"); ?>

<?php
// repalce the output files
$redirect_to = "blast_output_multiple.php";

// Collect all POST parameters
$params = $_POST;

// Default to 3000ms
$delay = isset($_GET['delay']) ? intval($_GET['delay']) : 3000; 
?>

<div class="loading-container">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden"></span>
    </div>
    <p class="mt-3">Processing your request. Please wait...</p>
</div>


<!-- creates a hidden HTML form that is used to forward POST data to blast_output.php -->
<form id="redirectForm" action="<?php echo $redirect_to; ?>" method="post" style="display: none;">
    <?php foreach ($params as $key => $value): ?>
        <?php if (is_array($value)): ?>
            <?php foreach ($value as $subValue): ?>
                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>[]" value="<?php echo htmlspecialchars($subValue); ?>">
            <?php endforeach; ?>
        <?php else: ?>
            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
        <?php endif; ?>
    <?php endforeach; ?>
</form>

<?php include_once realpath("$easy_gdb_path/footer.php"); ?>

<style>
    .loading-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        text-align: center;
        
    }
    .spinner-border {
        width: 3rem;
        height: 3rem;     
    }
</style>

<script>
    // Submit the form after a short delay
    setTimeout(function () {
        document.getElementById('redirectForm').submit();
    }, <?php echo $delay; ?>);
</script>
