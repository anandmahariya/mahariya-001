<? if(!empty($search_results['spelling'])){ ?> 
<p> 
    Did you mean <a href="/search?term=<?=$search_results['spelling']?>"><?=$search_results['spelling']?></a>? 
</p> 
<? } ?> 


<div> 

<p> 
<?=$search_results['result_text']?> 
</p> 

<? 
foreach($search_results['results'] as $result) 
{ 
?> 
<div> 
    <h3><a href="<?=$result['link']?>"><?=$result['title']?></a></h3> 
    <p> 
        <?=$result['description']?><br /> 
        <a href="<?=$result['link']?>"><?=$result['link']?></a> 
    </p> 
</div> 
<? 
} 
?> 

<p><?=$search_results['paging']?></p> 
</div> 