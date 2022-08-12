 
<div class="row">
	<div class="col-lg-12">
   News Tittle<br><hr> 
   {foreach from=$id_news item=$Tittle key=$key}
      {$params = ['id_news' => {$Tittle['id_news']|escape:'html_all':'UTF-8'}]}
         <p><a href="{$link->getModuleLink('mymodule', 'fronttask', $params)|escape:'html_all':'UTF-8'}">{$Tittle['tittle']|escape:'html_all':'UTF-8'}</a></p><br><hr>
    {/foreach}  
 	</div>
</div>
