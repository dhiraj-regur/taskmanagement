<?php /* Smarty version 2.6.19, created on 2020-02-27 14:35:37
         compiled from index.html */ ?>
<script src="https://unpkg.com/react@16.8/umd/react.production.min.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@16.8/umd/react-dom.production.min.js" crossorigin></script>
<script src="https://unpkg.com/babel-standalone@6/babel.min.js" crossorigin></script>
<script src="https://unpkg.com/axios/dist/axios.min.js" crossorigin></script>

<script src="https://unpkg.com/react-router/umd/react-router.min.js" crossorigin></script>
<script src="https://unpkg.com/react-router-dom/umd/react-router-dom.min.js" crossorigin></script>


<link href="/front/css/style.css" rel="stylesheet">

<div id="root"></div>


<?php echo '

<script type="text/babel" language="javascript">
var KEYCODE_ENTER = 13; 
var KEYCODE_ESC = 27;
var KEYCODE_TAB = 9;
var APP_URL = "/taskmanagement";

var Link = ReactRouterDOM.Link;
var Route = ReactRouterDOM.Route;
var Router = ReactRouterDOM.BrowserRouter;
var Switch = ReactRouterDOM.Switch;
var useParams = ReactRouterDOM.useParams;
var browserHistory = ReactRouterDOM.browserHistory;
var Redirect = ReactRouterDOM.Redirect;
//var HttpsRedirect = ReactRouterDOM.HttpsRedirect;


/*
Fetch project and task data from DB
*/
class ProjectTaskFetch extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      userId: 1,
      allTabItems: [],
      projectsInfo: [],
      fetchdata: false,
      projectId: props.projectId,
    }
  }

  async componentDidMount() {
    const userId = this.state.userId // TODO-we may grab the ID from the URL
    var self = this;

    await axios.get(`${APP_URL}/projecttasks/${userId}`)
              .then(response => {
                const tabinfo = Array.from(new Set(response.data.map(p=>p.projectId)))
                                .map(id=> {
                                  return {
                                    id: id,
                                    projectName: response.data.find(p => p.projectId == id).projectName,
                                    userId: response.data.find(p => p.projectId == id).userId,

                                  }
                                })
                                var activeProjectId = tabinfo[0].id;
                                if(self.state.projectId !== \'\') {
                                  activeProjectId = self.state.projectId;
                                } 

                                self.setState({
                                  allTabItems: tabinfo,
                                  projectsInfo: response.data,
                                  projectId: activeProjectId,
                                  fetchdata: true
                                })
              }).catch(function (error) {
                  //Display project fetch failed!
                  console.log(\'No project exist! Add one to start. Or look in error for more detail =>\' + error);
                  self.setState({fetchdata: true});
              });

      //TODO- if no tab then set default empty project(tab and board ready).
      /*if(Object.entries(this.state.allTabItems).length === 0) {
        const newTab = {id:\'NEW\', projectName:\'Untitled Project\', userId: this.state.userId}
          this.setState(
            { allTabItems: [...this.state.allTabItems, newTab]}
          )
      }*/
  }

  render() {
    return (
      <div className="app">
        {this.state.fetchdata === true && 
        <App
          allTabItems={this.state.allTabItems}
          projectsInfo={this.state.projectsInfo}
          userId={this.state.userId}
          projectId={this.state.projectId}
        />
        }
      </div>
    )
  }

}

/*
Process data and return Projects
*/
class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      tabItems: props.allTabItems,
      projectsInfo: props.projectsInfo,
      addTab:true,
      userId: props.userId,
      activeProjectId: props.projectId,
      newProject: [],
      redirect: false,
     // history: 
    }
  }


  componentWillReceiveProps(nextProps) {
   this.setState({ tabItems: nextProps.allTabItems  });  
  }

  updateAfterProjectDelete = (deleteProjectId) => {
    //udpate state tabItems
    var projectTemp = this.state.tabItems.filter(function(obj) {
          return obj.id != deleteProjectId;
    });

    
    var newActiveProjectId = this.state.activeProjectId;
    if(this.state.activeProjectId == deleteProjectId && this.state.tabItems.length > 0) {
        newActiveProjectId =  projectTemp[0].id;
    } 
    //console.log(newActiveProjectId);

    this.setState({ activeProjectId: newActiveProjectId} ) 
    /*if(Object.entries(this.state.tabItems).length === 0) {
      const newTab = {id:\'NEW\', projectName:\'Untitled Project\', userId: this.state.userId}
        this.setState(
          { tabItems: [...this.state.tabItems, newTab]}
        )
    }*/
  }

  addNewproject = () => {

    //this shall add new project with the name \'untitle proejct\' and create board means send new data to Project component
    var { [Object.keys(this.state.tabItems).pop()]: lastItem } = this.state.tabItems;
    if(Object.entries(this.state.tabItems).length === 0 || lastItem.id !== \'NEW\') {
      //check if any other project have autofocus property and remove it
      var tabItemsTemp = this.state.tabItems.filter(function(obj) {
        if(obj.hasOwnProperty("autofocus")) {
          delete obj.autofocus;
        }
        return obj;
      });
      //TODO -recheck
      
      //insert into db
      const newTabValue = {id:\'NEW\', projectName:\'Untitled Project\', userId: this.state.userId, autofocus: \'autofocus\'}

      var url = APP_URL + \'/updateproject/\';
      const encodeForm = (newTabValue) => {
        return Object.keys(newTabValue)
            .map(key => encodeURIComponent(key) + \'=\' + encodeURIComponent(newTabValue[key]))
            .join(\'&\');
      }
      var self=this;
      axios.post(url, encodeForm(newTabValue), {headers: {\'Accept\': \'application/json\'}})
        .then(function (response) {
            //setState itemId for new record
            const newProject = response.data;
            self.setState(
              { 
                tabItems: [...self.state.tabItems, newProject], 
                activeProjectId: newProject.id,
                redirect: true,
              }
            )
        })
        .catch(function (error) {
          //Display project add failed!
        });
    }
  }


  projectChange = (clickedprojectId) => {
    this.setState({activeProjectId: clickedprojectId})
  }

  render() {
    var self = this;
    var projectExist =false;
    var redirect =  APP_URL + "?projectId=" + this.state.activeProjectId;
    return (
      <React.Fragment>
        {this.state.redirect === true && 
          <ReactRouterDOM.BrowserRouter>
            <Redirect to={redirect} />
          </ReactRouterDOM.BrowserRouter>
        }
        
        <button className="insertTab" onClick={() => this.addNewproject() }>
          <strong> + </strong>
        </button>

        {this.state.tabItems.map(function(item, index) {
          var projectTabitem = item;
          const projectTasks = [];
          self.state.projectsInfo.map(function(item, index) {
              if(item.projectId  === projectTabitem.id && item.id.length > 0) {
                projectTasks.push(item);
              }
          });
          if(self.state.activeProjectId == item.id) { 
            projectExist = true;
          }
          
          return <Project 
                    key={index}
                    tabItem={item} 
                    projectTaskInfo={projectTasks}
                    addNewproject={self.addNewproject}
                    projectChange={self.projectChange}
                    activeProjectId={self.state.activeProjectId}
                    deleteproject={self.updateAfterProjectDelete}
                    allTabItems={self.state.tabItems}
                  />
        })}

        {projectExist === false && this.state.tabItems.length !== 0 && 
        <div class="message">
          <br />
          <p>Invalid Project Id! Please recheck projectId. 
          May be it is deleted. 
          Or else you can create new!</p>
        </div>
        }
        {this.state.tabItems.length === 0 && 
        <div class="message"><br /><br />
          <p>You have not added any project yet. 
            Please add one by clicking on "+" button at top right.
          </p>
        </div>
        }
      </React.Fragment>
    )
  }
}


/*
tabs and board will be here
*/
class Project extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      tabItem: props.tabItem,
      projectTaskInfo: props.projectTaskInfo,
      projectId: props.tabItem.id,
      activeProjectId: props.activeProjectId,
      addNewproject: props.addNewproject,
      projectChange: props.projectChange,
      deleteproject: props.deleteproject,
      //showProject: true,
      activeTab: false,
      allTabItems: props.allTabItems,
      deleteId: 0,
    }
  }

  componentWillReceiveProps(nextProps) { //without this Project will not render with new activeProjectId value
    this.setState({ activeProjectId: nextProps.activeProjectId, tabItem: nextProps.tabItem});  
  }

  
  removeProject = (deleteProjectId) => {
    this.setState({
      //showProject: false,
      deleteId: deleteProjectId
    });
    this.state.deleteproject(deleteProjectId);
  }

  projectChange = (clickedprojectId) => {
    this.state.projectChange(clickedprojectId);

  }

  render() {
    const ui = []; const un = [];
    const ni = []; const nn = [];

    this.state.projectTaskInfo.forEach((item, index) => {
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


    var activeTab = (this.state.projectId == this.state.activeProjectId) ? true : false;
    var showProject = true;
    if(this.state.deleteId == this.state.projectId) {
      showProject = false;
    }
    var noProjectBoard = true;
    this.state.allTabItems.forEach((item, index) => {
      if(item.id == this.state.activeProjectId) {
        noProjectBoard = false;
      }
    })

    return (
      <React.Fragment>
        {showProject === true && 
          <div class="Project">
          <Tab 
              tabitem={this.state.tabItem}
              tabId={this.state.projectId}
              projectChange={this.projectChange}
              addNewProjectManage={this.state.addNewproject}
              removeProject={this.removeProject}
              activeTab={activeTab}
              activeTabId={this.state.activeProjectId}
          />
          <div 
            className="board projectBoard"
            style={{display: this.state.projectId == this.state.activeProjectId ? \'\' : \'none\' }}
          >
            <div className="div1"> </div>
            <div className="div2"> <p>Important</p></div>
            <div className="div3"> <p>Not Important</p></div>
            <div className="div4"> <p className="verticaltext">Urgent</p></div>
            <div className="div5"> <Quadrant type="ui" quadrantTasks={ui} projectId={this.state.projectId} /></div>
            <div className="div6"> <Quadrant type="un" quadrantTasks={un} projectId={this.state.projectId} /></div>
            <div className="div7"> <p className="verticaltext">Not Urgent</p></div>
            <div className="div8"> <Quadrant type="ni" quadrantTasks={ni} projectId={this.state.projectId} /></div>
            <div className="div9"> <Quadrant type="nn" quadrantTasks={nn} projectId={this.state.projectId} /></div>
          </div>
        </div>
        }
 
    </React.Fragment>
    )
  }
}

/*
Contains projectTabs
*/
class Tab extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isInEditMode: false,
      tab: props.tabitem,
      tabId: props.tabitem.id,
      projectTabName: props.tabitem.projectName,
      projectChangeRequest: props.projectChange,
      addNewProjectManage: props.addNewProjectManage,
      removeProject: props.removeProject,
      activeTab: props.activeTab,
      activeTabId: props.activeTabId

    }
    this.setWrapperRef = this.setWrapperRef.bind(this);
    this.handleClickOutside = this.handleClickOutside.bind(this);
  }  

  setWrapperRef(node) {
    this.wrapperRef = node;
  }

  componentWillReceiveProps(nextProps) { //without this activeTab state chnage will not rener Tab
    this.setState({ activeTab: nextProps.activeTab, activeTabId: nextProps.activeTabId});
  }

  componentDidMount() {
    if(this.state.tabId=="NEW") {//Adding new project
      this.setState({isInEditMode: true}, function() {
          this.wrapperRef.focus();
      });
    }
    document.addEventListener(\'mousedown\', this.handleClickOutside);
  }

  componentWillUnmount() {
    document.removeEventListener(\'mousedown\', this.handleClickOutside);
  }

  handleClickOutside(event) {
    if (this.wrapperRef && !this.wrapperRef.contains(event.target)  && !event.target.classList.contains(\'projectTabEdit\')) {
      this.updateProjectName();

      //also change the editmode
      this.changeEditMode();
    }
  }

  handleChange = (e) => { 
    this.setState({ projectTabName: e.target.value });
  }

  changeEditMode =()=>{
    if(this.state.isInEditMode === false) {
      this.setState({isInEditMode: !this.state.isInEditMode}, function() {
        this.wrapperRef.focus();
      });
    } else {
      this.setState({
        isInEditMode: !this.state.isInEditMode
      })
    }
  }

  moveCaretAtEnd = (e) => {
    var temp_value = e.target.value
    e.target.value = \'\'
    e.target.value = temp_value
  }

  handleKeyDown = (e) => { 
    if (e.keyCode === KEYCODE_ENTER || e.keyCode === KEYCODE_TAB) {
      this.changeEditMode();
      this.setState({autofocus: \'\'})
      //save also
      this.updateProjectName();
    }
    if(e.keyCode === KEYCODE_ESC) {
      //Do not save and close new input
      this.setState({ isInEditMode: !this.state.isInEditMode })
      if(this.state.tabId=="NEW") {
        this.setState({projectTabName: \'\'})
      } else {
        this.setState({projectTabName: this.state.tab.projectTabName})
      }
    }
  }

  updateProjectName = () => {
    var projectName = this.state.projectTabName;
    if(projectName.length === 0 ) {
      projectName =  \'Untitled Project\'; //for db
      this.setState({ projectTabName: projectName}) //for state 
    }

    const newTabValue  = {
      id: this.state.tab.id,
      projectName: projectName,
      userId: this.state.tab.userId
    };

    var url = APP_URL + \'/updateProject/\';
    const encodeForm = (newTabValue) => {
      return Object.keys(newTabValue)
          .map(key => encodeURIComponent(key) + \'=\' + encodeURIComponent(newTabValue[key]))
          .join(\'&\');
    }
    axios.post(url, encodeForm(newTabValue), {headers: {\'Accept\': \'application/json\'}})
        .then(function (response) {
          self.setState({ tab: response.data})
        }).catch(function (error) {
            //Display task item update failed!
        });
  }

  projectTabClick = (e) => {
    e.preventDefault();
    if(!e.target.classList.contains(\'close\')) {
      this.state.projectChangeRequest(this.state.tabId);
    }
  }

  projectClose = (e) => {
    //e.preventDefault();
    const deleteTabValue  = {
      id: this.state.tab.id,
      projectName: this.state.projectTabName,
      userId: this.state.tab.userId
    };
    
    var url = APP_URL + \'/deleteproject/\';
    const encodeForm = (deleteTabValue) => {
      return Object.keys(deleteTabValue)
          .map(key => encodeURIComponent(key) + \'=\' + encodeURIComponent(deleteTabValue[key]))
          .join(\'&\');
    }
    var self = this;
    axios.post(url, encodeForm(deleteTabValue), {headers: {\'Accept\': \'application/json\'}})
        .then(function (response) {
          self.state.removeProject(response.data);
        })
        .catch(function (error) {
            //Display task item update failed!
        });
  }

  renderEditView =()=>{

    return (
      
        <input 
        className="projectTabEdit"
        type="text"
        autofocus
        onFocus={this.moveCaretAtEnd}
        defaultValue={this.state.projectTabName}
        ref={this.setWrapperRef}
        onChange={this.handleChange}
        onKeyDown={this.handleKeyDown}
      />
     
      )
  }

  renderTextView =()=>{
    var className = (this.state.activeTab === true) ? "projectTab activeTab" : "projectTab";
    className = className + " tabId-"+this.state.tabId;
    var linkto = APP_URL + "?projectId=" + this.state.tabId;
    var afterCloseParams = "?projectId=" + this.state.activeTabId;
    if(this.state.activeTabId === this.state.tabId) {
      afterCloseParams = \'\';
    }
    var closeTablink = APP_URL + afterCloseParams;
    return (
      <ReactRouterDOM.BrowserRouter>
        <div 
          onClick={this.projectTabClick}
          onDoubleClick={this.changeEditMode} 
          className={className}
        >
        <Link to={linkto}>
          <label>
              {this.state.projectTabName}
              <Link to={closeTablink}>
                <span 
                  class=\'close\'
                  onClick={this.projectClose}
                >
                  x
                </span>
              </Link>
          </label>
        </Link>
        </div>
      </ReactRouterDOM.BrowserRouter>
      )
  }

  render() {
    return ( 
      this.state.isInEditMode ? 
      this.renderEditView() :
      this.renderTextView()
    )
  }
}


/*
* Quadrant as per Urgent and Important divison
*/
class Quadrant extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      addItem: true,
      type: props.type,
      //userId:props.userId,
      projectId: props.projectId,
      tasks: props.quadrantTasks,
      insertItemClass: \'insertItem\'
    }
  }


  addNewItem=(childAddNewItemState, childItem, editAndInsertClick = false) => {
    //Get last item in taskitems
    var { [Object.keys(this.state.tasks).pop()]: lastItem } = this.state.tasks;
    if(this.state.addItem === true && childAddNewItemState === true){//ready to add \'NEW\' blank item
      if(Object.entries(this.state.tasks).length === 0 || lastItem.id !== \'NEW\') { 
        var urgent = 1; var important = 1;
        if(this.state.type ==\'ui\') {  urgent=1;  important = 1;}
        if(this.state.type ==\'un\') {  urgent=1;  important = 0;}
        if(this.state.type ==\'ni\') {  urgent=0;  important = 1;}
        if(this.state.type ==\'nn\') {  urgent=0;  important = 0;}
        const newItem = {id: \'NEW\', task: \'\', projectId: this.state.projectId, urgent:urgent, important:important, type:this.state.type, autofocus:\'autofocus\'};
        this.setState(
          { tasks: [...this.state.tasks, newItem]}
        )
      }
    } else { //remove NEW item
      var taskTemp = this.state.tasks.filter(function(obj) {
          return obj.id !== \'NEW\';
      });
      //add child item in task state
      if(Object.entries(childItem).length !== 0) {
        taskTemp =  [...taskTemp, childItem];
      }
      this.setState({ tasks: taskTemp} )
    }

    //change state addItem
    if(childAddNewItemState === true && editAndInsertClick === false) { 
      this.setState({ addItem: !this.state.addItem })
    }
  }


  render() {
    var addNewItemFunction = this.addNewItem;
    var addTaskTextToggle = (this.state.addItem === true) ? \'Add new task item here\' : \'Cancel adding task\';
    var insertItemClassToggle = (this.state.addItem === true) ? \'insertItem-\'+this.state.type : \'cancelInsert\';
    
    return (
      <div>
        {this.state.tasks.map(function(item, index){
          return <Item 
                      key={index} 
                      taskitem={item} 
                      updateItemState={addNewItemFunction} 
                      insertItemClass={insertItemClassToggle}
                  />
        })}
        <div className={insertItemClassToggle} onClick={() => this.addNewItem(true, []) }>{addTaskTextToggle}</div>
      </div>
    )
  }

}


/*
* Individual Task Item Box
* Either task text or Editable task item
*/
class Item extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isInEditMode: false,
      item: props.taskitem,
      itemId: props.taskitem.id,
      task: props.taskitem.task,
      className: props.taskitem.type,
      addNewManage: props.updateItemState,
      insertItemClass: props.insertItemClass,
      setEditInsertClick: \'insertItem-\'+ props.taskitem.type,
      cancelInsert:\'cancelInsert\',
    }

    this.setWrapperRef = this.setWrapperRef.bind(this);
    this.handleClickOutside = this.handleClickOutside.bind(this);
  }

  componentDidMount() {
    if(this.state.itemId=="NEW") {//Adding new task
      this.setState({isInEditMode: true}, function() {
          this.wrapperRef.focus();
      });
    }
    document.addEventListener(\'mousedown\', this.handleClickOutside);
  }

  componentWillUnmount() {
    document.removeEventListener(\'mousedown\', this.handleClickOutside);
  }
  
  handleClickOutside(event) { 
    if (this.wrapperRef && !this.wrapperRef.contains(event.target) && !event.target.classList.contains(this.state.cancelInsert)) {
      if(this.state.task.length > 0 || this.state.itemId !== \'NEW\') {
        this.updateItemValue();
      } 
      if(this.state.task.length === 0 && this.state.itemId === \'NEW\') {
        this.state.addNewManage(true, []);
      }
      if(event.target.classList.contains(this.state.setEditInsertClick)) {
        this.state.addNewManage(true, [], true);
      }

      this.changeEditMode();
    }
  }


  
  setWrapperRef(node) {
    this.wrapperRef = node;
  }

  updateItemValue =()=>{
    //Update in DB and render in item
    const newItemValue  = {
      id: this.state.item.id,
      task: this.state.task,
      projectId: this.state.item.projectId,
      urgent: this.state.item.urgent,
      important:this.state.item.important
    };
    
    var url = APP_URL + \'/updatetask/\';
    const encodeForm = (newItemValue) => {
      return Object.keys(newItemValue)
          .map(key => encodeURIComponent(key) + \'=\' + encodeURIComponent(newItemValue[key]))
          .join(\'&\');
    }
    var self = this;
    var additemsState = true;
    if(this.state.item.id !== \'NEW\' && !event.target.classList.contains(this.state.insertItemClass)) additemsState = false;
    var newChildren =\'\';
    axios.post(url, encodeForm(newItemValue), {headers: {\'Accept\': \'application/json\'}})
        .then(function (response) {
            if(additemsState === true) newChildren = response.data;

            //communicating with Quadrant
            self.state.addNewManage(additemsState, newChildren);
            //setState itemId for new record
            self.setState({
              item: response.data,
              autofocus:\'\'
            })
        })
        .catch(function (error) {
            //Display task item update failed!
    });
  }

  changeEditMode =()=>{
    if(this.state.isInEditMode === false) {
      this.setState({isInEditMode: !this.state.isInEditMode}, function() {
        this.wrapperRef.focus();
      });
    } else {
      this.setState({
        isInEditMode: !this.state.isInEditMode
      })
    }
  }

  handleChange = (e) => { 
    this.setState({ task: e.target.value });
  }
  handleKeyDown = (e) => {

    if (e.keyCode === KEYCODE_ENTER || e.keyCode === KEYCODE_TAB) {
      this.changeEditMode();
      //save also
      if(this.state.task.length > 0 || this.state.itemId !== \'NEW\') {
        this.updateItemValue();
      }
    }
    if(e.keyCode === KEYCODE_ESC) {
      //Do not save and close new input
      this.setState({ isInEditMode: !this.state.isInEditMode })
      if(this.state.itemId=="NEW") {
        this.setState({task: \'\', autofocus:\'\'})
      } else {
        this.setState({task: this.state.item.task})
      }
      this.state.addNewManage(true, []);
    }
  }


  moveCaretAtEnd =(e) => {
      var temp_value = e.target.value
      e.target.value = \'\'
      e.target.value = temp_value
  }

  renderEditView =()=>{
    return (
      <div>
        <input 
        id={this.state.className}
        className={this.state.className}
        type="text"
        autofocus
        onFocus={this.moveCaretAtEnd}
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


const Taskmanagement = () => (
  <React.Fragment>
    <Router>
      <React.Fragment>
        <Route
          path="/"
          render={({ location }) => {
            const { projectId } = getParams(location);
            return <ProjectTaskFetch projectId={projectId}  />;
          }}
        />
      </React.Fragment>
    </Router>
  </React.Fragment>
);

function getParams(location) {
  const searchParams = new URLSearchParams(location.search);
  return {
    projectId: searchParams.get("projectId") || ""
  };
}

ReactDOM.render(
  <Taskmanagement />,
  document.getElementById(\'root\')
);
</script>
'; ?>