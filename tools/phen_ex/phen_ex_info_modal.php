<!-- Phenotype Extraction help dialog -->
<div class="modal fade" id="phen_ex_help" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Phenotype Extraction — Help</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <ul class="text-left">
          <li>
            <strong>Select species:</strong> Choose the species whose phenotype data you want to use.
            Each species has its own phenotype file with different traits available.
          </li>
          <li>
            <strong>Select dataset:</strong> Some species have multiple phenotype datasets
            (e.g. leaves, fruit, inflorescence). Select the tissue or developmental stage
            you want to analyse.
          </li>
          <li>
            <strong>Select traits:</strong> Choose one or more phenotypic traits to include in the output CSV.
            Traits are colour-coded by type — blue (quantitative), orange (ordinal or binary), purple (nominal).
          </li>
          <li>
            <strong>Select accessions:</strong> Browse the list or paste a list of accession IDs, one per line.
            Only accessions present in the phenotype file will be matched.
          </li>
          <li>
            <strong>Nominal traits</strong> (text values such as color or shape) are automatically converted
            to dummy variables (0/1 columns, one per category) compatible with GAPIT.
          </li>
          <li>
            <strong>Output format:</strong> The downloaded CSV uses <code>Taxa</code> as the first column,
            as required by GAPIT. Accessions without phenotype data are included with <code>NA</code> values.
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
