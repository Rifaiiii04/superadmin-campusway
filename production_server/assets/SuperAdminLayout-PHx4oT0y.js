import{r as u,X as f,j as e,x as h}from"./app-ZrEbd4JQ.js";import{B as x}from"./building-2-Cp92zF51.js";import{X as j}from"./x-NGPg8wIZ.js";import{c as s}from"./createLucideIcon-eXmKYiaX.js";import{F as b}from"./file-text-BE8Bx8Cv.js";/**
 * @license lucide-react v0.542.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const v=[["path",{d:"M12 7v14",key:"1akyts"}],["path",{d:"M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z",key:"ruj8y"}]],N=s("book-open",v);/**
 * @license lucide-react v0.542.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const k=[["path",{d:"M8 2v4",key:"1cmpym"}],["path",{d:"M16 2v4",key:"4m81vk"}],["rect",{width:"18",height:"18",x:"3",y:"4",rx:"2",key:"1hopcy"}],["path",{d:"M3 10h18",key:"8toen8"}]],w=s("calendar",k);/**
 * @license lucide-react v0.542.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const M=[["path",{d:"M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z",key:"j76jl0"}],["path",{d:"M22 10v6",key:"1lu8f3"}],["path",{d:"M6 12.5V16a6 3 0 0 0 12 0v-3.5",key:"1r8lef"}]],_=s("graduation-cap",M);/**
 * @license lucide-react v0.542.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const S=[["path",{d:"M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8",key:"5wwlr5"}],["path",{d:"M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z",key:"1d0kgt"}]],A=s("house",S);/**
 * @license lucide-react v0.542.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const $=[["path",{d:"m16 17 5-5-5-5",key:"1bji2h"}],["path",{d:"M21 12H9",key:"dn1m92"}],["path",{d:"M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4",key:"1uf3rs"}]],C=s("log-out",$);/**
 * @license lucide-react v0.542.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const z=[["path",{d:"M4 12h16",key:"1lakjw"}],["path",{d:"M4 18h16",key:"19g7jn"}],["path",{d:"M4 6h16",key:"1o0s65"}]],L=s("menu",z);function V({children:p}){var c,d,i,l;const[r,o]=u.useState(!1),{auth:a}=f().props,g=[{name:"Dashboard",href:"/",icon:A},{name:"Sekolah",href:"/schools",icon:x},{name:"Jurusan",href:"/major-recommendations",icon:_},{name:"Bank Soal",href:"/questions",icon:N},{name:"Hasil Tes",href:"/results",icon:b},{name:"Jadwal TKA",href:"/tka-schedules",icon:w}],y=()=>{o(!r)},n=()=>{o(!1)};return e.jsxs("div",{className:"min-h-screen bg-gray-50",children:[r&&e.jsx("div",{className:"fixed inset-0 z-40 lg:hidden bg-black bg-opacity-50",onClick:n}),e.jsxs("div",{className:`fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out ${r?"translate-x-0":"-translate-x-full lg:translate-x-0"}`,children:[e.jsxs("div",{className:"flex items-center justify-between h-16 px-6 border-b border-gray-200",children:[e.jsxs("div",{className:"flex items-center",children:[e.jsx(x,{className:"h-8 w-8 text-maroon-600"}),e.jsx("span",{className:"ml-3 text-xl font-bold text-gray-900",children:"Super Admin"})]}),e.jsx("button",{onClick:n,className:"lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100",children:e.jsx(j,{className:"h-6 w-6"})})]}),e.jsx("nav",{className:"mt-6 px-3",children:e.jsx("div",{className:"space-y-1",children:g.map(t=>{const m=window.location.pathname===t.href;return e.jsxs(h,{href:t.href,onClick:n,className:`group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${m?"bg-maroon-100 text-maroon-700 border-r-2 border-maroon-700":"text-gray-600 hover:bg-gray-50 hover:text-gray-900"}`,children:[e.jsx(t.icon,{className:`mr-3 h-5 w-5 ${m?"text-maroon-700":"text-gray-400 group-hover:text-gray-500"}`}),t.name]},t.name)})})}),e.jsx("div",{className:"absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200",children:e.jsxs("div",{className:"flex items-center justify-between",children:[e.jsxs("div",{className:"flex items-center",children:[e.jsx("div",{className:"h-8 w-8 bg-maroon-100 rounded-full flex items-center justify-center",children:e.jsx("span",{className:"text-sm font-medium text-maroon-700",children:((i=(d=(c=a==null?void 0:a.user)==null?void 0:c.username)==null?void 0:d.charAt(0))==null?void 0:i.toUpperCase())||"A"})}),e.jsxs("div",{className:"ml-3",children:[e.jsx("p",{className:"text-sm font-medium text-gray-700",children:((l=a==null?void 0:a.user)==null?void 0:l.username)||"Admin"}),e.jsx("p",{className:"text-xs text-gray-500",children:"Super Admin"})]})]}),e.jsx(h,{href:"/logout",method:"post",as:"button",className:"p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md",children:e.jsx(C,{className:"h-5 w-5"})})]})})]}),e.jsxs("div",{className:"lg:ml-64",children:[e.jsx("div",{className:"sticky top-0 z-30 bg-white shadow-sm border-b border-gray-200 top-bar",children:e.jsxs("div",{className:"flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8",children:[e.jsx("button",{onClick:y,className:"lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100",children:e.jsx(L,{className:"h-6 w-6"})}),e.jsx("div",{className:"flex-1 lg:hidden"}),e.jsx("div",{className:"flex items-center space-x-4",children:e.jsx("div",{className:"hidden sm:block",children:e.jsx("p",{className:"text-sm text-gray-500",children:new Date().toLocaleDateString("id-ID",{weekday:"long",year:"numeric",month:"long",day:"numeric"})})})})]})}),e.jsx("main",{className:"min-h-screen",children:p})]})]})}export{N as B,w as C,_ as G,V as S};
