import React, { Component } from 'react'
import request from "adios/Request";
import { ProgressBar } from 'primereact/progressbar';

export interface Panel {
  title: string,
  boardUrlSlug: string,
  configuration: any,
  contentLoaded?: boolean,
  content?: string,
}

export interface DesktopDashboardProps {
  panels: Array<Panel>
}

export interface DesktopDashboardState {
  panels: Array<Panel>,
}

export default class DesktopDashboard extends Component<DesktopDashboardProps, DesktopDashboardState> {

  constructor(props: DesktopDashboardProps) {
    super(props);

    this.state = {
      panels: this.props.panels,
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
          panels[i].boardUrlSlug,
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
      <div className="card-header">{panel.title}</div>
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
      <div className="flex gap-2">
        <div className="flex flex-col gap-2">
          {panelsLeft.map((panel: Panel, index: any) => this.renderPanel(panel, index))}
        </div>
        <div className="flex flex-col gap-2">
          {panelsRight.map((panel: Panel, index: any) => this.renderPanel(panel, index))}
        </div>
      </div>
    </>
  }

}