/*datagrid默认分页格式*/
$.fn.pagination.defaults.beforePageText = '第'; 
$.fn.pagination.defaults.afterPageText = '共{pages}页'; 
$.fn.pagination.defaults.displayMsg = '显示{from}到{to},共{total}条记录'; 
$.fn.pagination.defaults.layout = ['list','manual','sep','first','prev','links','next','last','sep','refresh']; 
$.fn.pagination.defaults.links = 5; 
/*格式化时间*/
$.fn.datebox.defaults.timeformat = function(date){
	var ts = arguments[0] || 0;
            var t, y, m, d, h, i, s;
            t = ts ? new Date(ts * 1000) : new Date();
            y = t.getFullYear();
            m = t.getMonth() + 1;
            d = t.getDate();
            h = t.getHours();
            i = t.getMinutes();
            s = t.getSeconds();
            // 可根据需要在这里定义时间格式  
            return y + '-' + (m < 10 ? '0' + m : m) + '-' + (d < 10 ? '0' + d : d) + ' ' + (h < 10 ? '0' + h : h) + ':' + (i < 10 ? '0' + i : i) + ':' + (s < 10 ? '0' + s : s);
};
