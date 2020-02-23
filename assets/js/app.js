// Window bindings
import CodeMirror from 'codemirror';
console.log(CodeMirror)
window.CodeMirror = CodeMirror

import jQuery from 'jquery';
window.$ = jQuery

// Load HTML mixed mode for syntax highlighting.
import 'codemirror/mode/htmlmixed/htmlmixed'
import 'codemirror/mode/javascript/javascript'

// Bootstrap for tabs etc.
import 'bootstrap'
