<?php /* Smarty version 2.6.19, created on 2020-02-11 13:24:43
         compiled from form.html */ ?>
<script src="https://unpkg.com/react@16/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<div id="root"></div>

<?php echo '
<style>
  body {
    font: 14px "Century Gothic", Futura, sans-serif;
    margin: 20px;
  }
  .parent {
    display: grid;
    grid-template-columns: 0.3fr repeat(2, 2fr) repeat(2, 1fr);
    grid-template-rows: 0.3fr repeat(2, 12fr) repeat(2, 1fr);
    grid-column-gap: 0px;
    grid-row-gap: 0px;
    }

    .div1 { grid-area: 1 / 1 / 2 / 2; }
    .div2 { grid-area: 1 / 2 / 2 / 3; text-align: center;background-color: #B7B7B7; font-weight: bold; }
    .div3 { grid-area: 1 / 3 / 2 / 4; text-align: center;background-color: #B7B7B7; font-weight: bold; }
    .div4 { grid-area: 2 / 1 / 3 / 2; text-align: center;background-color: #B7B7B7; font-weight: bold;}
    .div5 { grid-area: 2 / 2 / 3 / 3; background-color: #FF0000; padding:5px;}
    .div6 { grid-area: 2 / 3 / 3 / 4; background-color: #D9EAD3; padding:5px;}
    .div7 { grid-area: 3 / 1 / 4 / 2; text-align: center;background-color: #B7B7B7; font-weight: bold;}
    .div8 { grid-area: 3 / 2 / 4 / 3; background-color: #D9EAD3; padding:5px;}
    .div9 { grid-area: 3 / 3 / 4 / 4; background-color: #D9D9D9; padding:5px;}
  
  .ui {
    background-color: #FF0000;
  }
  .un {
    background-color: #D9EAD3;
  }
  .ni {
    background-color: #D9EAD3;
  }
  .nn {
    background-color: #D9D9D9;
  }
  input {
      width: 100%;
  }
  .insertItem {
    background-color: rgb(163, 235, 205);
    opacity: 0.8;
  }
  .insertItem:hover {
    background-color: rgb(238, 232, 143);
    cursor: pointer;
    opacity: 1;
  }
</style> 
<script type="text/babel" language="javascript">

class Item extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      item: props.taskitem,
      itemId:props.taskitem.id,
      task:props.taskitem.task,
      className:props.taskitem.type,
      isInEditMode:false
    }

    this.setWrapperRef = this.setWrapperRef.bind(this);
    this.handleClickOutside = this.handleClickOutside.bind(this);
  }

  componentDidMount() {
    if(this.state.itemId=="NEW") {//Adding new task
      this.setState({
        isInEditMode: true
      })
    }
    document.addEventListener(\'mousedown\', this.handleClickOutside);
  }

  componentWillUnmount() {
    document.removeEventListener(\'mousedown\', this.handleClickOutside);
  }

  handleClickOutside(event) {
    if (this.wrapperRef && !this.wrapperRef.contains(event.target)) {
      //You clicked outside this item
      this.changeEditMode();
      //update this item
      this.updateItemValue();
    }
  }
  
  setWrapperRef(node) {
    this.wrapperRef = node;
  }

  updateItemValue =()=>{
    //Update in DB and render in item
    const newItemValue  = {
      id: this.state.itemId,
      task: this.state.task,
      userId: this.state.item.userId,
      urgent: this.state.item.urgent,
      important:this.state.item.important
    };
    
    var url = \'/task/update/\';
    const encodeForm = (newItemValue) => {
      return Object.keys(newItemValue)
          .map(key => encodeURIComponent(key) + \'=\' + encodeURIComponent(newItemValue[key]))
          .join(\'&\');
    }
    var self = this;
    axios.post(url, encodeForm(newItemValue), {headers: {\'Accept\': \'application/json\'}})
        .then(function (response) {
            //TODO-Display task item is updated on DOM
            //setState itemId for new record
            self.setState({
              itemId: response.data
            })
        })
        .catch(function (error) {
            //Display task item update failed!
    });

  }

  changeEditMode =()=>{
    this.setState({
      isInEditMode: !this.state.isInEditMode
    })
  }
  handleChange = (e) => {
    this.setState({ task: e.target.value });
  }
  handleKeyDown = (e) => {
    if (e.key === \'Enter\' || e.key ===\'Tab\') {
      this.changeEditMode();
      //save also
      this.updateItemValue();
    }
  }

  renderEditView =()=>{
    return (
      <div>
        <input 
        className={this.state.className}
        type="text"
        defaultValue={this.state.task}
        ref={this.setWrapperRef}
        onChange={this.handleChange}
        onKeyDown={this.handleKeyDown}
      />
      </div>
      )
  }

  renderTextView =()=>{
    return (
      <div onDoubleClick={this.changeEditMode}>
        {this.state.task}
      </div>
      )
  }

  render() {
    return (
      this.state.isInEditMode  ?
      this.renderEditView() :
      this.renderTextView()
    )
  }

}

class Quadrant extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      type:props.type,
      tasks:props.quadrantTasks,
      userId:props.userId,
      //addItem:true,
    }
  }

  addNewItem=() =>{ //Add new Item
      var urgent = 1; var important = 1;
      if(this.state.type ==\'ui\') {  urgent=1;  important = 1;}
      if(this.state.type ==\'un\') {  urgent=1;  important = 0;}
      if(this.state.type ==\'ni\') {  urgent=0;  important = 1;}
      if(this.state.type ==\'nn\') {  urgent=0;  important = 0;}
      const newItem = {id: \'NEW\', task: \'\', userId: this.state.userId, urgent:urgent, important:important, type:this.state.type};
      this.setState(
        { tasks: [...this.state.tasks, newItem] }
      )
      //this.setState({ addItem : !this.state.addItem} );
  }

  render() {
    /*var addTaskTextToggle;
    if(this.state.addItem) { 
      addTaskTextToggle = \'Add new task item here\';
    } else {
      addTaskTextToggle = \'Cancel adding task\';
    }*/
    return (
      <div>
        {this.state.tasks.map(function(item, index){
          return <Item key={index} taskitem={item} />;
        })}
        <div className="insertItem" onClick={this.addNewItem}>Add new task item here</div>
      </div>
    )
  }

}

class Board extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      taskitem:props.items,
      userId:props.userId
		}
  }

  render() {
    //Separating taskitems in quadrant from their urgent and important value
    //adding element \'type\' for css issue
    const ui = []; const un = [];
    const ni = []; const nn = [];
    this.state.taskitem.forEach((item, index) => {
        if(item.urgent==1 && item.important==1) { 
              item.type = \'ui\'; ui.push(item);
        }
        if(item.urgent==1 && item.important==0) {
            item.type = \'un\'; un.push(item);
        }
        if(item.urgent==0 && item.important==1) {
            item.type = \'ni\'; ni.push(item);
        }
        if(item.urgent==0 && item.important==0) {
            item.type = \'nn\'; nn.push(item);
        }
      });

      return (
      <div className="parent">
        <div className="div1"> </div>
        <div className="div2"> Important</div>
        <div className="div3"> Not Important</div>
        <div className="div4"> <p className="verticaltext">Urgent</p></div>
        <div className="div5"> <Quadrant type="ui" quadrantTasks={ui} userId={this.state.userId} /></div>
        <div className="div6"> <Quadrant type="un" quadrantTasks={un} userId={this.state.userId} /></div>
        <div className="div7"> Not Urgent</div>
        <div className="div8"> <Quadrant type="ni" quadrantTasks={ni} userId={this.state.userId} /></div>
        <div className="div9"> <Quadrant type="nn" quadrantTasks={nn} userId={this.state.userId} /></div>
      </div>
    );
  }  

}

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      allTaskItems:null,
      userId:1
    }
  }

  componentWillMount() { //Fetch all task items from DB of an user and do setState
    axios.get(\'/task/list/\'+this.state.userId)
    .then(response => {
      this.setState({allTaskItems: response.data})
    })
    .catch(err => console.log(err))
  }

  render() {
    return ( 
      <div className="tasks">
        <div className="tasks-board">
          {this.state.allTaskItems !==null && <Board items={this.state.allTaskItems} userId={this.state.userId}/>}
        </div>
      </div>
    );
  }
}

ReactDOM.render(
  <App />,
  document.getElementById(\'root\')
);
</script>
'; ?>