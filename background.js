chrome.contextMenus.create({
  "title" : "suggest keyword",
  "type"  : "normal",
  "contexts" : ["all"],
	"onclick": hoge
	},
	function(){
});

function hoge(info, tab){
	var word = info.selectionText;
	chrome.storage.sync.get("a", function(data){
		window.open('http://trezia.db.ics.keio.ac.jp/yuna/suggest_word/sotsuron/matome.php?word='+word);
	});
};
