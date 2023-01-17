import React from 'react'

export default function App() {
  return <div></div>
}


if (document.getElementById('app')) {
  ReactDOM.render(<Provider store={store}><App /></Provider>, document.getElementById('app'));
}
