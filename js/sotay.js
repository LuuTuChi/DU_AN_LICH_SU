var NOTES=[
  {id:1,title:'Mốc thời gian Cách mạng tháng Tám',content:'13/8/1945: Quyết định Tổng KN\n16/8/1945: Đại hội Quốc dân Tân Trào\n19/8/1945: KN Hà Nội\n23/8/1945: KN Huế\n25/8/1945: KN Sài Gòn\n2/9/1945: Tuyên ngôn Độc lập',tag:'ls12',color:'nc-yellow',date:'20/03/2025'},
  {id:2,title:'Nguyên nhân thắng lợi CM tháng Tám',content:'Chủ quan: Lãnh đạo đúng đắn của ĐCS Đông Dương, tinh thần yêu nước, sự chuẩn bị lâu dài.\nKhách quan: Nhật Bản đầu hàng Đồng minh, hệ thống thực dân–phát xít suy yếu.',tag:'qt',color:'nc-green',date:'19/03/2025'},
  {id:3,title:'Ý nghĩa lịch sử',content:'Đối với VN: Lật đổ chế độ thực dân–phong kiến, mở kỷ nguyên độc lập.\nĐối với TG: Cổ vũ phong trào giải phóng dân tộc.',tag:'kn',color:'nc-blue',date:'18/03/2025'},
  {id:4,title:'Câu hỏi cần ôn tập',content:'1. Tại sao CM tháng Tám là cuộc CM dân tộc dân chủ?\n2. So sánh CM 1945 với các cuộc khởi nghĩa trước.\n3. Vai trò của Mặt trận Việt Minh.',tag:'bt',color:'nc-pink',date:'17/03/2025'},
];
var tagLabels={ls12:'Lịch Sử 12',bt:'Bài tập',qt:'Quan trọng',kn:'Kiến thức'};
var tagClasses={ls12:'tag-ls12',bt:'tag-bt',qt:'tag-qt',kn:'tag-kn'};
var activeTag='all',editId=null,selColor='nc-yellow',wSelected=null;
function renderTagFilter(){
  var html='<button class="tag-btn '+(activeTag==='all'?'active':'')+'" onclick="filterTag(this,\'all\')">Tất cả</button>';
  Object.keys(tagLabels).forEach(function(t){
    html+='<button class="tag-btn '+(activeTag===t?'active':'')+'" onclick="filterTag(this,\''+t+'\')">'+tagLabels[t]+'</button>';
  });
  document.getElementById('tagFilter').innerHTML=html;
}
function filterTag(btn,t){activeTag=t;renderTagFilter();renderNotes();}
function renderNotes(){
  var q=document.getElementById('noteSearch').value.toLowerCase();
  var list=NOTES.filter(function(n){
    return (activeTag==='all'||n.tag===activeTag)&&(n.title.toLowerCase().includes(q)||n.content.toLowerCase().includes(q));
  });
  var g=document.getElementById('notesGrid'),e=document.getElementById('emptyNotes');
  if(!list.length){g.innerHTML='';e.style.display='block';return;}
  e.style.display='none';
  g.innerHTML=list.map(function(n){
    return '<div class="note-card '+n.color+'">'
      +'<span class="note-tag '+tagClasses[n.tag]+'">'+tagLabels[n.tag]+'</span>'
      +'<div class="note-title">'+n.title+'</div>'
      +'<div class="note-content">'+n.content.replace(/\n/g,'<br>')+'</div>'
      +'<div class="note-footer"><span class="note-date">📅 '+n.date+'</span>'
      +'<div class="note-actions">'
      +'<button class="note-btn" onclick="editNote('+n.id+',event)" title="Sửa">✏️</button>'
      +'<button class="note-btn" onclick="deleteNote('+n.id+',event)" title="Xóa">🗑️</button>'
      +'</div></div></div>';
  }).join('');
}
function filterNotes(){renderNotes();}
function openAdd(){editId=null;selColor='nc-yellow';document.getElementById('modalTitle').textContent='Ghi chú mới';document.getElementById('fTitle').value='';document.getElementById('fContent').value='';document.getElementById('fTag').value='ls12';document.querySelectorAll('.cp-opt').forEach(function(c){c.classList.remove('selected');});document.querySelector('[data-color="nc-yellow"]').classList.add('selected');document.getElementById('modalOverlay').classList.add('open');}
function editNote(id,e){
  e.stopPropagation();
  var n=NOTES.find(function(x){return x.id===id;});if(!n)return;
  editId=id;selColor=n.color;
  document.getElementById('modalTitle').textContent='Sửa ghi chú';
  document.getElementById('fTitle').value=n.title;
  document.getElementById('fContent').value=n.content;
  document.getElementById('fTag').value=n.tag;
  document.querySelectorAll('.cp-opt').forEach(function(c){c.classList.remove('selected');if(c.dataset.color===n.color)c.classList.add('selected');});
  document.getElementById('modalOverlay').classList.add('open');
}
function deleteNote(id,e){
  e.stopPropagation();if(!confirm('Xóa ghi chú này?'))return;
  NOTES=NOTES.filter(function(n){return n.id!==id;});renderNotes();
}
function saveNote(){
  var title=document.getElementById('fTitle').value.trim();
  var content=document.getElementById('fContent').value.trim();
  if(!title){alert('Hãy nhập tiêu đề!');return;}
  if(!content){alert('Hãy nhập nội dung!');return;}
  var tag=document.getElementById('fTag').value;
  var today=new Date();var d=today.getDate(),m=today.getMonth()+1,y=today.getFullYear();
  var dateStr=(d<10?'0':'')+d+'/'+(m<10?'0':'')+m+'/'+y;
  if(editId){var n=NOTES.find(function(x){return x.id===editId;});if(n){n.title=title;n.content=content;n.tag=tag;n.color=selColor;n.date=dateStr;}}
  else{var newId=NOTES.length?Math.max.apply(null,NOTES.map(function(n){return n.id;}))+1:1;NOTES.unshift({id:newId,title:title,content:content,tag:tag,color:selColor,date:dateStr});}
  closeModal();renderNotes();
}
function pickColor(el){document.querySelectorAll('.cp-opt').forEach(function(c){c.classList.remove('selected');});el.classList.add('selected');selColor=el.dataset.color;}
function closeModal(){document.getElementById('modalOverlay').classList.remove('open');}
function closeBg(e){if(e.target===document.getElementById('modalOverlay'))closeModal();}
renderTagFilter();renderNotes();

