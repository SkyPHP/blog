 <div id="disqus_thread"></div>
    <script type="text/javascript">
     
     <? if($r['blog_article_ide']){
           //currently disqus js does not correctly support comment count for threads
           //that use disqus_identifier
           //when it does, remove '&& false'
      ?>
           var disqus_identifier = '<?=$disqus_identifier?>';
           // [Optional but recommended: Define a
           //unique identifier (e.g. post id or slug) for this thread]
     <? } ?>
       
     (function() {
      var dsq = document.createElement('script'); dsq.type =
    'text/javascript'; dsq.async = true;
      dsq.src = 'http://joonbug.disqus.com/embed.js';
      (document.getElementsByTagName('head')[0] ||
    document.getElementsByTagName('body')[0]).appendChild(dsq);
     })();
    </script>
