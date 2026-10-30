<?php include realpath('../header.php'); ?>
<?php include realpath('modal.html'); ?>
<?php
$multiple_blast_db = !empty($multiple_blast_db) ? 1 : 0;
$maximum = isset($max_extract_seq_input) ? (int) $max_extract_seq_input : 1000;
if ($maximum < 1) $maximum = 1000;
$maximum = min($maximum, 10000);
$action = $multiple_blast_db ? 'blast/blastdbcmd_multiple.php' : 'blast/blastdbcmd_single.php';
?>
<div class="margin-20">
  <a class="float-right" href="/easy_gdb/help/04_sequence_extraction.php" target="_blank" rel="noopener noreferrer"><i class="fa fa-info" style="font-size:20px;color:#229dff"></i> Help</a>
</div>
<br>
<h1 class="text-center">Sequence Extraction <i class="fas fa-download" style="color:#555"></i></h1>
<br>
<div class="form margin-20 tool-container" style="margin:auto">
  <form id="download_fasta_form" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" method="post">
    <div class="sequence_attr">
      <label for="txtDownloadGenes">Paste a list of gene IDs</label>
      <textarea class="form-control" id="txtDownloadGenes" rows="8" name="gids"><?= htmlspecialchars((string) ($input_gene_list ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>
    </div>
    <br>
    <div class="form-group">
      <?php include_once __DIR__ . '/blast/blast_dbs_select.php'; ?>
    </div>
    <?php if ($multiple_blast_db): ?>
      <input type="hidden" name="selected_dbs" id="selected_dbs" value="">
    <?php endif; ?>
    <div class="text-right">
      <button class="button btn btn-info" id="btnSend" type="submit">Download</button>
    </div>
  </form>
</div>
<br>
<?php include realpath('../footer.php'); ?>
<style>.margin-20 { margin: 20px; }</style>
<script>
$(function () {
    const multiple = <?= json_encode((bool) $multiple_blast_db) ?>;
    const maximum = <?= json_encode($maximum) ?>;
    $('#download_fasta_form').on('submit', function (event) {
        const raw = $('#txtDownloadGenes').val() || '';
        const ids = raw.split(/\r\n|[\n\r\u0085\u2028\u2029]/)
            .map(function (id) { return id.trim(); })
            .filter(function (id) { return id.length > 0; });
        let message = '';
        if (!ids.length) message = 'No gene IDs were provided.';
        else if (ids.length > maximum) message = 'A maximum of ' + maximum + ' gene IDs is allowed.';
        else if (ids.some(function (id) { return !/^[A-Za-z0-9_][A-Za-z0-9_.:|+\-]{0,254}$/.test(id); })) {
            message = 'One or more gene IDs have an invalid format.';
        }
        if (!message && multiple) {
            const selected = [];
            $('input[name="blast_db[]"]:checked').each(function () { selected.push($(this).val()); });
            if (!selected.length || selected.length > 20) message = 'Select between 1 and 20 databases.';
            else $('#selected_dbs').val(selected.join(','));
        }
        if (message) { event.preventDefault(); window.alert(message); }
    });
});
</script>
