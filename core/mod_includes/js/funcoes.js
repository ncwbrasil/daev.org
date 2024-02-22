﻿
function shareFacebook(url)
{

        var w = 630;
        var h = 360;

        var left = 2150 ;
        var top = screen.height/2 - 360/2;

        window.open('http://www.facebook.com/sharer.php?u='+url, 'Compartilhar no facebook', 'toolbar=no, location=no, directories=no, status=no, ' + 'menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width='+w+ ', height=' + h + ', top=' + top + ', left=' + left);
 }

function moeda( obj , e )
{
    var tecla = ( window.event ) ? e.keyCode : e.which;
    if ( tecla == 8 || tecla == 0 )
        return true;
    if ( tecla != 46 && tecla < 48 || tecla > 57 )
        return false;
}


function media(valor,disciplinas)
{
	var m;
	var soma=0.0;
	for(m=0; m < disciplinas; m++)
	{
		if(jQuery("#not_nota_"+m).val() != '')
		{
			var teste = 1;
			soma = soma + parseFloat(jQuery("#not_nota_"+m).val());
		}
	}
	
	if(teste == 1)
	{
		jQuery("#rnk_soma").val(soma.toFixed(2));
	}
	else
	{
		jQuery("#rnk_soma").val("");
	}
}
function carregaBusca(valor)
{
	jQuery("#buscar").val('');
	jQuery("#buscar").val(valor);
	jQuery('#suggestions').hide();
	jQuery("#autoSuggestionsList").html("");
	
}
function carregaBuscaClientePF(valor, id)
{
	jQuery("#clientepf").val('');
	jQuery("#clientepf").val(valor);
	jQuery("#anu_clientepf").val(id);
	jQuery('#suggestions').hide();
	jQuery("#autoSuggestionsList").html("");
	
}
function carregaBuscaClientePJ(valor, id)
{
	jQuery("#clientepj").val('');
	jQuery("#clientepj").val(valor);
	jQuery("#anu_clientepj").val(id);
	jQuery('#suggestions').hide();
	jQuery("#autoSuggestionsList").html("");
	
	
}
//----------------------------------------MASCARAS-----------------------------------------------//

function mascaraData(inputData, e)
{   
	var data = inputData.value;
	
		
		if(data.length == 10)
		{
			return false;
		}
		else
		{
			if(document.all) // Internet Explorer
			var tecla = event.keyCode;
			else //Outros Browsers
			var tecla = e.which;
			if(tecla >= 48 && tecla < 58)
			{ // numeros de 0 a 9 e "/"
				var data = inputData.value;
				if (data.length == 2 || data.length == 5){
				data += '/';
				inputData.value = data;
			}
			}
			else if(tecla == 8 || tecla == 0) // Backspace, Delete e setas direcionais(para mover o cursor, apenas para FF)
			return true;
			else
			return false;
		}

}

function verificaTorre(inputData, e)
{
	var data = inputData.value;
		
		if(data.length == 10)
		{
			return false;
		}
		else
		{
			if(document.all) // Internet Explorer
			var tecla = event.keyCode;
			else //Outros Browsers
			var tecla = e.which;
			if(tecla >= 48 && tecla <= 57)
			{ // numeros de 0 a 9 e "/"
				if (data.length > 3)
				{
					return false;
				
				}
				
			}
			else if(tecla >= 65 && tecla <= 90)
			{
				if (data.length >= 1)
				{
					return false;
				
				}
			}
			else if(tecla >= 97 && tecla <= 122)
			{
				if (data.length >= 1)
				{
					return false;
				
				}
			}
			else 
			{
				return false;
			}
			return true;
		}

}

function maskData(val) {
  var pass = val.value;
  var expr = /[0123456789]/;

  for (i = 0; i < pass.length; i++) {
    // charAt -> retorna o caractere posicionado no índice especificado
    var lchar = val.value.charAt(i);
    var nchar = val.value.charAt(i + 1);

    if (i == 0) {
      // search -> retorna um valor inteiro, indicando a posição do inicio da primeira
      // ocorrência de expReg dentro de instStr. Se nenhuma ocorrencia for encontrada o método retornara -1
      // instStr.search(expReg);
      if ((lchar.search(expr) != 0) || (lchar > 3)) {
        val.value = "";
      }

    } else if (i == 1) {

      if (lchar.search(expr) != 0) {
        // substring(indice1,indice2)
        // indice1, indice2 -> será usado para delimitar a string
        var tst1 = val.value.substring(0, (i));
        val.value = tst1;
        continue;
      }

      if ((nchar != '/') && (nchar != '')) {
        var tst1 = val.value.substring(0, (i) + 1);

        if (nchar.search(expr) != 0)
          var tst2 = val.value.substring(i + 2, pass.length);
        else
          var tst2 = val.value.substring(i + 1, pass.length);

        val.value = tst1 + '/' + tst2;
      }

    } else if (i == 4) {

      if (lchar.search(expr) != 0) {
        var tst1 = val.value.substring(0, (i));
        val.value = tst1;
        continue;
      }

      if ((nchar != '/') && (nchar != '')) {
        var tst1 = val.value.substring(0, (i) + 1);

        if (nchar.search(expr) != 0)
          var tst2 = val.value.substring(i + 2, pass.length);
        else
          var tst2 = val.value.substring(i + 1, pass.length);

        val.value = tst1 + '/' + tst2;
      }
    }

    if (i >= 6) {
      if (lchar.search(expr) != 0) {
        var tst1 = val.value.substring(0, (i));
        val.value = tst1;
      }
    }
  }

  if (pass.length > 10)
    val.value = val.value.substring(0, 10);
  return true;
}
function mascaraHorario(inputData, e)
{
	var data = inputData.value;
	
		if(document.all) // Internet Explorer
		var tecla = event.keyCode;
		else //Outros Browsers
		var tecla = e.which;
		
		if(tecla >= 48 && tecla < 58)
		{ // numeros de 0 a 9 e "/"
			var data = inputData.value;
			if (data.length == 2){
			data += ':';
			inputData.value = data;
		}
		}
		else if(tecla == 8 || tecla == 0) // Backspace, Delete e setas direcionais(para mover o cursor, apenas para FF)
		return true;
		else
		return false;
	
}
function mascaraCEP(campoCEP)
{
	var cep = campoCEP.value;
    if (cep.length == 5)
    {
    	cep = cep + '-';
        campoCEP.value = cep;
	    return true;             
    }
}
function mascaraPlaca(campoPlaca)
{
	var cep = campoPlaca.value;
    if (cep.length == 3)
    {
    	cep = cep + '-';
        campoPlaca.value = cep;
	    return true;             
    }
}

function mascaraRG(campoRG)
{
	var reg = campoRG.value;
    if (reg.length == 2)
    {
    	reg = reg + '.';
        campoRG.value = reg;
	    return true;              
    }
    if (reg.length == 6)
    {
        reg = reg + '.';
        campoRG.value = reg;
        return true;
    }
	 if (reg.length == 10)
    {
        reg = reg + '-';
        campoRG.value = reg;
        return true;
    }
}

function mascaraCPF(campoCPF)
{
	var cpf = campoCPF.value;
    if (cpf.length == 3)
    {
    	cpf = cpf + '.';
        campoCPF.value = cpf;
	    return true;              
    }
    if (cpf.length == 7)
    {
        cpf = cpf + '.';
        campoCPF.value = cpf;
        return true;
    }
	 if (cpf.length == 11)
    {
        cpf = cpf + '-';
        campoCPF.value = cpf;
        return true;
    }
}
function mascaraCNPJ(campoCNPJ)
{
	var cnpj = campoCNPJ.value;
    if (cnpj.length == 2)
    {
    	cnpj = cnpj + '.';
        campoCNPJ.value = cnpj;
	    return true;              
    }
    if (cnpj.length == 6)
    {
        cnpj = cnpj + '.';
        campoCNPJ.value = cnpj;
        return true;
    }
	if (cnpj.length == 10)
    {
        cnpj = cnpj + '/';
        campoCNPJ.value = cnpj;
        return true;
    }
	 if (cnpj.length == 15)
    {
        cnpj = cnpj + '-';
        campoCNPJ.value = cnpj;
        return true;
    }
}
/// INICIO MASCARA TELEFONE ///
function mascara(o,f){
    v_obj=o
    v_fun=f
    setTimeout("execmascara()",1)
}
function execmascara(){
    v_obj.value=v_fun(v_obj.value)
}
function mtel(v){
    v=v.replace(/\D/g,"");             //Remove tudo o que não é dígito
    v=v.replace(/^(\d{2})(\d)/g,"($1) $2"); //Coloca parênteses em volta dos dois primeiros dígitos
    v=v.replace(/(\d)(\d{4})$/,"$1-$2");    //Coloca hífen entre o quarto e o quinto dígitos
    return v;
}
function id( el ){
	return document.getElementById( el );
}
function mascaraTELEFONE(campo)
{		
	mascara( campo, mtel );
}

/// FIM MASCARA TELEFONE ///

function SomenteNumero(e)
{
	var tecla=(window.event)?event.keyCode:e.which;
	if((tecla > 47 && tecla < 58))
		return true;
		else
		{
			if (tecla != 8)
				return false;
			else
			return true;
		}
}
function SomenteNumeroRG(e)
{
	var tecla=(window.event)?event.keyCode:e.which;
	if((tecla > 47 && tecla < 58 || tecla == 88 || tecla == 120))
		return true;
		else
		{
			if (tecla != 8)
				return false;
			else
			return true;
		}
}
function SomenteNumeroCEL(inputData, e)
{
	var campo = inputData.value;
	if(campo.length == 15)
	{
		return false;
	}
	else
	{
		var tecla=(window.event)?event.keyCode:e.which;
		if((tecla > 47 && tecla < 58 ) || tecla == 32)
		return true;
		else
		{
			if (tecla != 8)
				return false;
			else
			return true;
		}
	}
}
function MascaraMoeda(objTextBox, SeparadorMilesimo, SeparadorDecimal, e){

    var sep = 0;
    var key = '';
    var i = j = 0;
    var len = len2 = 0;
    var strCheck = '0123456789';
    var aux = aux2 = '';
    var whichCode = (window.Event) ? e.which : e.keyCode;
    if (whichCode == 13) return true;
    key = String.fromCharCode(whichCode); // Valor para o código da Chave
    if (strCheck.indexOf(key) == -1) return false; // Chave inválida
    len = objTextBox.value.length;
    for(i = 0; i < len; i++)
        if ((objTextBox.value.charAt(i) != '0') && (objTextBox.value.charAt(i) != SeparadorDecimal)) break;
    aux = '';
    for(; i < len; i++)
        if (strCheck.indexOf(objTextBox.value.charAt(i))!=-1) aux += objTextBox.value.charAt(i);
    aux += key;
    len = aux.length;
    if (len == 0) objTextBox.value = '';
    if (len == 1) objTextBox.value = '0'+ SeparadorDecimal + '0' + aux;
    if (len == 2) objTextBox.value = '0'+ SeparadorDecimal + aux;
    if (len > 2) {
        aux2 = '';
        for (j = 0, i = len - 3; i >= 0; i--) {
            if (j == 3) {
                aux2 += SeparadorMilesimo;
                j = 0;
            }
            aux2 += aux.charAt(i);
            j++;
        }
        objTextBox.value = '';
        len2 = aux2.length;
        for (i = len2 - 1; i >= 0; i--)
        objTextBox.value += aux2.charAt(i);
        objTextBox.value += SeparadorDecimal + aux.substr(len - 2, len);
    }
    return false;
}

//----------------------------------------FIM MASCARAS-----------------------------------------------//



//--------------------------------------FUNCOÇES GERAIS----------------------------------------------//

function limpar(campo) 
{
	campo.value = '';
}

function voltar() 
{
	window.history.back();
}
//------------------------------------FIM FUNCOÇES GERAIS--------------------------------------------//



function abrir(URL) {

  var width = 920;
  var height = 500;

  var left = 99;
  var top = 99;

  window.open(URL,'janela', 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=no, fullscreen=no');

}

function aumentarZoom() 
{
	if (window.parent.document.body.style.zoom != undefined && window.parent.document.body.style.zoom != 0)
	{
		window.parent.document.body.style.zoom *= 1.05
	}
	else
	{
		window.parent.document.body.style.zoom = 1.05
	}
}

function zerarZoom()
{
	window.parent.document.body.style.zoom = 1
}

function diminuirZoom()
{
	if (window.parent.document.body.style.zoom != undefined && window.parent.document.body.style.zoom != 0)
	{
		window.parent.document.body.style.zoom /= 1.05
	}
	else
	{
		window.parent.document.body.style.zoom = 1 / 1.05
	}
}


!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a("object"==typeof exports?require("jquery"):jQuery)}(function(a){var b,c=navigator.userAgent,d=/iphone/i.test(c),e=/chrome/i.test(c),f=/android/i.test(c);a.mask={definitions:{9:"[0-9]",a:"[A-Za-z]","*":"[A-Za-z0-9]"},autoclear:!0,dataName:"rawMaskFn",placeholder:"_"},a.fn.extend({caret:function(a,b){var c;if(0!==this.length&&!this.is(":hidden"))return"number"==typeof a?(b="number"==typeof b?b:a,this.each(function(){this.setSelectionRange?this.setSelectionRange(a,b):this.createTextRange&&(c=this.createTextRange(),c.collapse(!0),c.moveEnd("character",b),c.moveStart("character",a),c.select())})):(this[0].setSelectionRange?(a=this[0].selectionStart,b=this[0].selectionEnd):document.selection&&document.selection.createRange&&(c=document.selection.createRange(),a=0-c.duplicate().moveStart("character",-1e5),b=a+c.text.length),{begin:a,end:b})},unmask:function(){return this.trigger("unmask")},mask:function(c,g){var h,i,j,k,l,m,n,o;if(!c&&this.length>0){h=a(this[0]);var p=h.data(a.mask.dataName);return p?p():void 0}return g=a.extend({autoclear:a.mask.autoclear,placeholder:a.mask.placeholder,completed:null},g),i=a.mask.definitions,j=[],k=n=c.length,l=null,a.each(c.split(""),function(a,b){"?"==b?(n--,k=a):i[b]?(j.push(new RegExp(i[b])),null===l&&(l=j.length-1),k>a&&(m=j.length-1)):j.push(null)}),this.trigger("unmask").each(function(){function h(){if(g.completed){for(var a=l;m>=a;a++)if(j[a]&&C[a]===p(a))return;g.completed.call(B)}}function p(a){return g.placeholder.charAt(a<g.placeholder.length?a:0)}function q(a){for(;++a<n&&!j[a];);return a}function r(a){for(;--a>=0&&!j[a];);return a}function s(a,b){var c,d;if(!(0>a)){for(c=a,d=q(b);n>c;c++)if(j[c]){if(!(n>d&&j[c].test(C[d])))break;C[c]=C[d],C[d]=p(d),d=q(d)}z(),B.caret(Math.max(l,a))}}function t(a){var b,c,d,e;for(b=a,c=p(a);n>b;b++)if(j[b]){if(d=q(b),e=C[b],C[b]=c,!(n>d&&j[d].test(e)))break;c=e}}function u(){var a=B.val(),b=B.caret();if(o&&o.length&&o.length>a.length){for(A(!0);b.begin>0&&!j[b.begin-1];)b.begin--;if(0===b.begin)for(;b.begin<l&&!j[b.begin];)b.begin++;B.caret(b.begin,b.begin)}else{for(A(!0);b.begin<n&&!j[b.begin];)b.begin++;B.caret(b.begin,b.begin)}h()}function v(){A(),B.val()!=E&&B.change()}function w(a){if(!B.prop("readonly")){var b,c,e,f=a.which||a.keyCode;o=B.val(),8===f||46===f||d&&127===f?(b=B.caret(),c=b.begin,e=b.end,e-c===0&&(c=46!==f?r(c):e=q(c-1),e=46===f?q(e):e),y(c,e),s(c,e-1),a.preventDefault()):13===f?v.call(this,a):27===f&&(B.val(E),B.caret(0,A()),a.preventDefault())}}function x(b){if(!B.prop("readonly")){var c,d,e,g=b.which||b.keyCode,i=B.caret();if(!(b.ctrlKey||b.altKey||b.metaKey||32>g)&&g&&13!==g){if(i.end-i.begin!==0&&(y(i.begin,i.end),s(i.begin,i.end-1)),c=q(i.begin-1),n>c&&(d=String.fromCharCode(g),j[c].test(d))){if(t(c),C[c]=d,z(),e=q(c),f){var k=function(){a.proxy(a.fn.caret,B,e)()};setTimeout(k,0)}else B.caret(e);i.begin<=m&&h()}b.preventDefault()}}}function y(a,b){var c;for(c=a;b>c&&n>c;c++)j[c]&&(C[c]=p(c))}function z(){B.val(C.join(""))}function A(a){var b,c,d,e=B.val(),f=-1;for(b=0,d=0;n>b;b++)if(j[b]){for(C[b]=p(b);d++<e.length;)if(c=e.charAt(d-1),j[b].test(c)){C[b]=c,f=b;break}if(d>e.length){y(b+1,n);break}}else C[b]===e.charAt(d)&&d++,k>b&&(f=b);return a?z():k>f+1?g.autoclear||C.join("")===D?(B.val()&&B.val(""),y(0,n)):z():(z(),B.val(B.val().substring(0,f+1))),k?b:l}var B=a(this),C=a.map(c.split(""),function(a,b){return"?"!=a?i[a]?p(b):a:void 0}),D=C.join(""),E=B.val();B.data(a.mask.dataName,function(){return a.map(C,function(a,b){return j[b]&&a!=p(b)?a:null}).join("")}),B.one("unmask",function(){B.off(".mask").removeData(a.mask.dataName)}).on("focus.mask",function(){if(!B.prop("readonly")){clearTimeout(b);var a;E=B.val(),a=A(),b=setTimeout(function(){B.get(0)===document.activeElement&&(z(),a==c.replace("?","").length?B.caret(0,a):B.caret(a))},10)}}).on("blur.mask",v).on("keydown.mask",w).on("keypress.mask",x).on("input.mask paste.mask",function(){B.prop("readonly")||setTimeout(function(){var a=A(!0);B.caret(a),h()},0)}),e&&f&&B.off("input.mask").on("input.mask",u),A()})}})});