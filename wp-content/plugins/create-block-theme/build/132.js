"use strict";(globalThis.webpackChunkcreate_block_theme=globalThis.webpackChunkcreate_block_theme||[]).push([[132],{9132:(t,s,n)=>{n.r(s),n.d(s,{name:()=>r}),n(6770);var i=n(2592),e=n(7047);class r extends i.x{constructor(t,s){const{p:n}=super(t,s);this.format=n.uint16,this.count=n.uint16,this.stringOffset=n.Offset16,this.nameRecords=[...new Array(this.count)].map((t=>new a(n,this))),1===this.format&&(this.langTagCount=n.uint16,this.langTagRecords=[...new Array(this.langTagCount)].map((t=>new o(n.uint16,n.Offset16)))),this.stringStart=this.tableStart+this.stringOffset}get(t){let s=this.nameRecords.find((s=>s.nameID===t));if(s)return s.string}}class o{constructor(t,s){this.length=t,this.offset=s}}class a{constructor(t,s){this.platformID=t.uint16,this.encodingID=t.uint16,this.languageID=t.uint16,this.nameID=t.uint16,this.length=t.uint16,this.offset=t.Offset16,(0,e.Z)(this,"string",(()=>(t.currentPosition=s.stringStart+this.offset,function(t,s){const{platformID:n,length:i}=s;if(0===i)return"";if(0===n||3===n){const s=[];for(let n=0,e=i/2;n<e;n++)s[n]=String.fromCharCode(t.uint16);return s.join("")}const e=t.readBytes(i),r=[];return e.forEach((function(t,s){r[s]=String.fromCharCode(t)})),r.join("")}(t,this))))}}}}]);