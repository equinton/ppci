<div class="container">
    <p class="text-muted hidden-xs hidden-sm">
    {$copyright}
<br>
{t}Pour tout problème :{/t} <a href="{$appliAssist}">{$appliAssist}</a>
</p>
 <ul class="nav pull-right scroll-top scrolltotop">
  <li><a href="#" title="Scroll to top"><i class="glyphicon glyphicon-chevron-up"></i></a></li>
</ul>
{if strlen($developpementMode) > 1}
<div class="text-warning">{$developpementMode}</div>
{/if}
  </div>
