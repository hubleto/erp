import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import { ProgressBar } from 'primereact/progressbar';
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import ModalForm from "@hubleto/react-ui/core/ModalForm";
import FormPanel from "./FormPanel";

export interface Panel {
  id: number,
  width: number,
  title: string,
  board_url_slug: string,
  configuration: any,
  contentLoaded?: boolean,
  content?: string,
}

export interface DesktopDashboardProps {
  idDashboard: number,
  panels: Array<Panel>
}

export interface DesktopDashboardState {
  panels: Array<Panel>,
  showIdPanel: number,
  draggedIdPanel: number,
  hidePanelsWhileDragging: boolean,
}

export default class DesktopDashboard extends TranslatedComponent<DesktopDashboardProps, DesktopDashboardState> {

  props: DesktopDashboardProps;
  state: DesktopDashboardState;

  translationContext: string = 'Hubleto\\App\\Community\\Dashboards\\Loader::Components\\Dashboard';

  constructor(props: DesktopDashboardProps) {
    super(props);

    this.state = {
      panels: this.props.panels,
      showIdPanel: 0,
      draggedIdPanel: 0,
      hidePanelsWhileDragging: false,
    }
  }

  componentDidMount() {
    this.loadPanelContents();
  }

  loadPanelContents() {
    let panels = this.state.panels;

    for (let i in panels) {
      let configuration = {};

      try {
        configuration = JSON.parse(panels[i].configuration ?? '');
      } catch (ex) {
        configuration = {};
      }

      if (!panels[i].contentLoaded) {
        request.get(
          panels[i].board_url_slug,
          configuration ?? {},
          (html: any) => {
            try {
              this.state.panels[i].contentLoaded = true;
              this.state.panels[i].content = html;
              this.setState({panels: panels});
            } catch (err) {
              console.error(err);
            }
          }
        );
      }
    }
  }

  setPanelWidth(idPanel: number, width: number) {
    console.log(idPanel, width);

    let newPanels = this.state.panels;
    for (let i in newPanels) {
      if (newPanels[i].id == idPanel) {
        newPanels[i].width = width;
      }
    }
    this.setState({panels: newPanels});

    request.get(
      'dashboards/api/set-panel-width',
      {
        idDashboard: this.props.idDashboard,
        idPanel: idPanel,
        width: width,
      },
      (result: any) => {
      }
    );
  }

  onDragStart(e: any, idPanel: number) {
    this.setState({ draggedIdPanel: idPanel });
    setTimeout(() => { this.setState({ hidePanelsWhileDragging: true }) }, 50);
    e.dataTransfer.effectAllowed = "move";
  };

  onDragOver(e: any, targetPanelId: number) {
    e.preventDefault();
    const draggedIdPanel = this.state.draggedIdPanel;
    const panels = this.state.panels;

    if (draggedIdPanel === targetPanelId) return;

    let draggedPanel: Panel = null;
    for (let i in panels) {
      if (panels[i].id === draggedIdPanel) draggedPanel = panels[i];
    }

    const newPanels = [];
    for (let i in panels) {
      if (panels[i].id == draggedIdPanel) continue;
      if (panels[i].id == targetPanelId) {
        newPanels.push(draggedPanel);
      }
      newPanels.push(panels[i]);
    }

    this.setState({ panels: newPanels });
  };

  onDrop(e: any) {
    e.preventDefault();

    request.get(
      'dashboards/api/save-panel-order',
      {
        idDashboard: this.props.idDashboard,
        panelOrder: this.state.panels.map((item: Panel) => item.id),
      },
      (html: any) => {
        this.setState({ draggedIdPanel: null, hidePanelsWhileDragging: false });
      }
    );
  };

  renderPanel(panel: any, index: any) {
    const width = panel.width ?? 1;
    return <div
      key={index}
      className={
        "card"
        + " " + (panel.id == this.state.draggedIdPanel ? "card-info" : "")
      }
      style={{gridColumn: `span ${width}`}}
    >
      <div
        className="card-header cursor-move"
        draggable
        onDragStart={(e: any) => this.onDragStart(e, panel.id)}
        onDragOver={(e: any) => this.onDragOver(e, panel.id)}
        onDrop={(e: any) => this.onDrop(e)}
      >
        <div className='btn-group items-center hidden md:block'>
          <button
            className='btn btn-transparent btn-small'
            onClick={() => {
              let newWidth = (panel.width ?? 3) - 1;
              if (newWidth > 6) newWidth = 6;
              if (newWidth < 1) newWidth = 1;
              this.setPanelWidth(panel.id, newWidth);
            }}
          >
            <span className='icon'><i className='fas fa-arrow-left'></i></span>
          </button>
          <button
            className='btn btn-transparent btn-small'
            onClick={() => {
              let newWidth = (panel.width ?? 3) + 1;
              if (newWidth > 6) newWidth = 6;
              if (newWidth < 1) newWidth = 1;
              this.setPanelWidth(panel.id, newWidth);
            }}
          >
            <span className='icon'><i className='fas fa-arrow-right'></i></span>
          </button>
        </div>
        {panel.title}
        <button
          className='btn btn-transparent btn-small'
          onClick={() => { this.setState({showIdPanel: panel.id}); }}
        >
          <span className='icon'><i className='fas fa-cog'></i></span>
        </button>
      </div>
      {this.state.hidePanelsWhileDragging ?
        <div className="card-body bg-gray-50 p-4"></div>
      : (panel.contentLoaded ? 
        <div className="card-body" dangerouslySetInnerHTML={{__html: panel.content}}></div>
      :
        <div className="card-body">
          <ProgressBar mode="indeterminate" style={{ height: '2em' }}></ProgressBar>
        </div>
      )}
    </div>
  }

  render() {
    setTimeout(() => {
      globalThis.main.renderReactElements();
    }, 100);

    const panels = this.state.panels;

    return <div className='flex flex-col gap-2'>
      <div className='block gap-2 md:grid md:grid-cols-6'>
        {panels.map((panel: Panel, index: any) => this.renderPanel(panel, index))}
      </div>
      <div>
        <button
          className='btn btn-add mt-2'
          onClick={() => { this.setState({showIdPanel: -1}); }}
        >
          <span className='icon'><i className='fas fa-plus'></i></span>
          <span className='text'>{this.translate('Add new panel')}</span>
        </button>
      </div>
      {this.state.showIdPanel != 0 ?
        <ModalForm
          uid='add_new_panel_modal'
          isOpen={true}
          type='right'
        >
          <FormPanel
            uid='add_new_panel_form'
            customEndpointParams={{idDashboard: this.props.idDashboard}}
            id={this.state.showIdPanel}
            showInModal={true}
            showInModalSimple={true}
            onClose={() => { this.setState({showIdPanel: 0}); }}
          />
        </ModalForm>
      : <></>}
    </div>
  }

}