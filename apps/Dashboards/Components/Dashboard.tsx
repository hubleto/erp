import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import { ProgressBar } from 'primereact/progressbar';
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import ModalForm from "@hubleto/react-ui/core/ModalForm";
import FormPanel from "./FormPanel";

export interface Panel {
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
}

export default class DesktopDashboard extends TranslatedComponent<DesktopDashboardProps, DesktopDashboardState> {

  props: DesktopDashboardProps;
  state: DesktopDashboardState;

  translationContext: string = 'HubletoApp\\Community\\Dashboards\\Loader::Components\\Dashboard';

  constructor(props: DesktopDashboardProps) {
    super(props);

    this.state = {
      panels: this.props.panels,
      showIdPanel: 0,
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

  renderPanel(panel: any, index: any) {
    return <div key={index} className="card">
      <div className="card-header">
        {panel.title}
        <button
          className='btn btn-transparent btn-small'
          onClick={() => { this.setState({showIdPanel: panel.id}); }}
        >
          <span className='icon'><i className='fas fa-cog'></i></span>
        </button>
      </div>
      {panel.contentLoaded ? 
        <div className="card-body" dangerouslySetInnerHTML={{__html: panel.content}}></div>
      :
        <div className="card-body">
          <ProgressBar mode="indeterminate" style={{ height: '2em' }}></ProgressBar>
        </div>
      }
    </div>
  }

  render() {
    setTimeout(() => {
      globalThis.main.renderReactElements();
    }, 100);

    const panels = this.props.panels;
    const panelsLeft = Array.from(panels.slice(0, Math.ceil(panels.length / 2)));
    const panelsRight = Array.from(panels.slice(Math.ceil(panels.length / 2)));

    return <>
      <div className="flex flex-col gap-2 md:flex-row">
        <div className="flex flex-col gap-2">
          {panelsLeft.map((panel: Panel, index: any) => this.renderPanel(panel, index))}
        </div>
        <div className="flex flex-col gap-2">
          {panelsRight.map((panel: Panel, index: any) => this.renderPanel(panel, index))}
        </div>
      </div>
      <button
        className='btn btn-add mt-2'
        onClick={() => { this.setState({showIdPanel: -1}); }}
      >
        <span className='icon'><i className='fas fa-plus'></i></span>
        <span className='text'>{this.translate('Add new panel')}</span>
      </button>
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
    </>
  }

}