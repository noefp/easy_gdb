var openGPlink = function(url, data) {
    // open g:Profiler with prepopulated values
    //
    // url - the tool we are targeting. Usually one of
    //  'https://biit.cs.ut.ee/gprofiler/gost'
    //  'https://biit.cs.ut.ee/gprofiler/convert'
    //  'https://biit.cs.ut.ee/gprofiler/orth'
    //  'https://biit.cs.ut.ee/gprofiler/snpense'
    //  Could also direct to beta ('https://biit.cs.ut.ee/gprofiler_beta/gost')
    //  or recent archives ('https://biit.cs.ut.ee/gprofiler_archive3/e100_eg47_p14/gost')
    //
    // data - javascript object with endpoint-appropriate fields filled
    //  see https://biit.cs.ut.ee/gprofiler/page/apis
    //
    // Example:
    //
    // openGPlink('https://biit.cs.ut.ee/gprofiler/convert',
    // {query: ['FBgn0016984'], organism:'dmelanogaster', target:'GO', numeric_namespace:'ENTREZGENE'}
    // )

    // add an invisible form to the DOM
    var form = document.createElement('form')
    // request gets made to the gplink service, no data is stored on the server
    form.setAttribute('action', 'https://biit.cs.ut.ee/gplink/p')
    form.setAttribute('method', 'post')
    form.setAttribute('name', 'gp_submit_form')
    form.setAttribute('id', 'gp_submit_form')
    form.setAttribute('target', '_blank')

    var payload = document.createElement('input')
    payload.setAttribute('type', 'text')
    payload.setAttribute('name', 'payload')
    payload.setAttribute('value', JSON.stringify(data))
    payload.setAttribute('hidden', '')

    var urlElement = document.createElement('input')
    urlElement.setAttribute('type', 'text')
    urlElement.setAttribute('name', 'url')
    urlElement.setAttribute('value', url)
    urlElement.setAttribute('hidden', '')

    form.appendChild(payload)
    form.appendChild(urlElement)

    document.body.appendChild(form)

    // submit the form. This should open g:Profiler in a new tab with values prepopulated.
    form.submit()

    // remove the form from DOM
    form.parentNode.removeChild(form)
}
