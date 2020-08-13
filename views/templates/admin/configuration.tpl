{* todo: launch onboarding and retreive datas *}
<button onclick="launchFBE()"> Launch FBE Workflow </button>
<script src="{$pathApp|escape:'htmlall':'UTF-8'}"></script>
<script>
var PsfacebookControllerLink="{$PsfacebookControllerLink}"
</script>

<style>
  /** Hide native multistore module activation panel, because of visual regressions on non-bootstrap content */
  #content.nobootstrap div.bootstrap.panel {
    display: none;
  }
</style>
