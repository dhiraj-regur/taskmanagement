<?php /* Smarty version 2.6.19, created on 2020-02-19 07:39:12
         compiled from add.html */ ?>
<script src="https://unpkg.com/react@16/umd/react.development.js"></script>
<script src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>
<script src="https://unpkg.com/prop-types/prop-types.js"></script>
<script src="https://unpkg.com/@babel/standalone@7/babel.min.js"></script>
<script src="https://unpkg.com/react-tabs@3/dist/react-tabs.development.js"></script>
<link href="https://unpkg.com/react-tabs@3/style/react-tabs.css" rel="stylesheet">
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/antd/4.0.0-rc.4/antd.js"></script> 

<link href="/front/css/style.css" rel="stylesheet">
<link rel="stylesheet" href="	https://cdnjs.cloudflare.com/ajax/libs/antd/4.0.0-rc.4/antd.css">

<!--<script src="https://unpkg.com/styled-components/dist/styled-components.min.js" type="text/javascript"></script>
<script src="https://unpkg.com/react-sortable-hoc/dist/umd/react-sortable-hoc.js"></script>
<script src="https://unpkg.com/classnames@2.2.5/index.js"></script>
<script src="https://unpkg.com/prop-types@15.6/prop-types.min.js"></script>
<script src="https://unpkg.com/react-tabtab/dist/react-tabtab.min.js"></script>
<script src="https://unpkg.com/react-tabs/dist/react-tabs.development.js"></script>
-->

 <!-- after webpack bundle we can keep separate components injs files -->
<!--<script type="text/jsx" src="/assets/js/reactjs/app.js"></script>-->

<div id="root"></div>

<?php echo '

<script type="text/babel" language="javascript">
var KEYCODE_ENTER = 13; 
var KEYCODE_ESC = 27;
var KEYCODE_TAB = 9;

const { Tag, Input, Tooltip, Icon, Tabs } = antd;
const { TabPane } = antd.Tabs;

class Board extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      project: props.project
    }
  }

  render(){
    return (
      <h1>{this.state.project}</h1>
    )
  }
}

class App extends React.Component {
constructor(props) {
  super(props);
  this.state = {
    userId: 1,
    tabs: [],//get all tabs/projects from database
    show: [],
    inputVisible: false,
    projectName:\'\'
  }
}



  componentWillMount() { 

    this.setState({tabs: [\'tab1\',\'tab2\',\'tab3\'], show:[true, false, false] })
  }
  onClickDisplay=(tab)=>{
    //set state true to display
    console.log(tab);
    //display only the content of this tab

  }

  addNewProject=()=>{
    console.log(\'add new project\');
    //let user edit the name
    //get the name
    //add new tab in tab state
    //store the name in db

  }

  showInput = () => {
    console.log(\'add new\');
      this.setState({ inputVisible: true,  projectName: \'\'});
      
  };

  showHide =(num)=> { console.log(num);
          this.setState((prevState) => {
              const temp = [...prevState.show];
              const newTabState=[];
              temp.forEach((item, index) => {
                newTabState.push(false);
              });
              newTabState[num] = true;
              return {show: newTabState};
          })
      }
    
  updateProjectValue =()=>{
    //project update will happen here
    console.log(\'in update\');
    console.log(this.state.projectName);
    this.setState((prevState) => {
    //  console.log(this.state.tabs);
    //  console.log(prevState.tabs);

      tabs: this.state.tabs.push(this.state.projectName)
    });
    //console.log(this.state.tab);

  }

  updateProjectName=()=>{
    //turn on edit clicked tab
    console.log(\'double click edit exist tab\');
    
    //update existing project tab name
    //console.log(e);
  }

  handleChange=(e)=> { console.log(e.target.value);
    this.setState({ projectName: e.target.value });
  }
  handleKeyDown=(e)=> {
    if (e.keyCode === KEYCODE_ENTER || e.keyCode === KEYCODE_TAB) {
      this.changeEditMode();
      //save also
      if(this.state.projectName.length > 0) {
        //save new project name in the db
        //if edit then update project name in db
        //add/update new tab in tab state 
        //display new project tab in 
        this.updateProjectValue();
      }
    }
    if(e.keyCode === KEYCODE_ESC) {
      this.changeEditMode();
    }
    
  }

  changeEditMode =()=>{
    this.setState({
      inputVisible: !this.state.inputVisible
    })
  }

  renderEditNewTab=()=>{
    return (
      <div>
          <input
          //id={} //project id to be use later in update
          className="addNewProject"
          type="text"
          //autofocus={this.state.autofocus}
          value={this.state.projectName}
          //ref={this.setWrapperRef}
          ref={(input) => { this.nameInput = input; }} 
          onChange={this.handleChange}
          onKeyDown={this.handleKeyDown}
        />
        </div>
    )
  }
  renderTextNewTab=()=>{
    return (
      <div 
        className="addNewProject" 
        //onClick={this.addNewProject} 
        onClick={this.showInput}
      >
        Add New
      </div>
    )
  }

  render() { //console.log(this.state.tabs);
    let tablist=[];
  // const {  inputVisible } = this.state;
    /*for(let i=0; i<4; i++) {
      tablist.push(<div key={i} className="projectTab" onClick={()=>this.showHide(i)}>Tab {i} </div>)
    }*/
    var addNewTab = 
    this.state.inputVisible  ?
        this.renderEditNewTab() :
        this.renderTextNewTab()

/*    var existingTab =
    this.state.tabInputVisible  ?
        this.renderEditTab() :
        this.renderTextTab()
*/
  //console.log(addNewOption);
    var self= this;
    return ( 
      <div className="container">
        <div className="tabsholder">
          {
          this.state.tabs.map(function(item, index){ //console.log(item);
            return <div 
                        key={index} 
                        className="projectTab" 
                        onClick={()=>self.showHide(index)}  
                        onDoubleClick={self.updateProjectName}
                    >
                      {item}
                    </div>
          })
          }
          
          {addNewTab}
        </div>
        <div>
          {
          this.state.tabs.map(function(item, index){
            return <div key={index} >{self.state.show[index] && <Board project={index}/>}</div>
          })
          }
        </div>
      </div>
    );
  }


}
ReactDOM.render(<App />, document.getElementById(\'root\'));
</script>
'; ?>