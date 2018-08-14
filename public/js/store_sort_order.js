$(function() {
    //jquery sortableの初期化と初期設定値の設定
    $(".sortable").sortable({
        //指定要素が入れ替わった時に発火
        update: function(ev, ui) {
            console.log(ev,ui);
            //指定要素の順番を配列化し、カンマ区切りの文字列に変換
            var updateArray =  $(".sortable").sortable("toArray").join(",");
            //変換された文字列をcookieに３０日保持で格納
            $.cookie("sortable", updateArray, {expires: 30});
            console.log("updateArray",updateArray);
        }       
    });
    //すでにsortableというcookieがあるかをチェック
    if($.cookie("sortable")) {
        //cookieのsortableをいうデータの値を取得し、カンマ区切り文字列から配列に変換後、配列を逆転する
        var cookieValue = $.cookie("sortable").split(",").reverse();
        console.log("cookieValue", cookieValue);
        //上記で取得した配列をループし、要素を追加
        $.each(cookieValue,function(index, value) {
            $('#'+value).prependTo(".sortable");
        });
        console.log("追加後cookieValue", cookieValue);
    }
});