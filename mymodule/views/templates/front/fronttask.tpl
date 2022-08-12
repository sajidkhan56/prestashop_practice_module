 
{extends file='page.tpl'}
{block name="content"}
<div class="row">
	<div class="col-lg-12">
    News Description <br><hr> 
    {foreach from=$description item=data}
     {$data['description']|escape:'html_all':'UTF-8'} <br><hr> 
    {/foreach}
  </div>
</div>
{/block}


    
    