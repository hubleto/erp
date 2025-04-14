import React, { Component } from 'react'
import request from "adios/Request";
import { ProgressBar } from 'primereact/progressbar';

export interface Board {
  title: string,
  rendererUrlSlug: string,
  contentLoaded?: boolean,
  content?: string,
}

export interface DesktopDashboardProps {
  boards: Array<Board>
}

export interface DesktopDashboardState {
  boards: Array<Board>,
}

export default class DesktopDashboard extends Component<DesktopDashboardProps, DesktopDashboardState> {

  constructor(props: DesktopDashboardProps) {
    super(props);

    this.state = {
      boards: this.props.boards,
    }
  }

  componentDidMount() {
    this.loadBoardContents();
  }

  loadBoardContents() {
    let boards = this.state.boards;

    for (let i in boards) {
      if (!boards[i].contentLoaded) {
        request.get(
          boards[i].rendererUrlSlug,
          {},
          (html: any) => {
            try {
              this.state.boards[i].contentLoaded = true;
              this.state.boards[i].content = html;
              this.setState({boards: boards});
            } catch (err) {
              console.error(err);
            }
          }
        );
      }
    }
  }

  render() {
    setTimeout(() => {
      globalThis.main.renderReactElements();
    }, 100);
    return <>
      <div className="grid md:grid-cols-2 gap-2">
        {this.props.boards.map((board: Board, index: any) => {
          return <div key={index} className="card">
            <div className="card-header">{board.title}</div>
            {board.contentLoaded ? 
              <div className="card-body" dangerouslySetInnerHTML={{__html: board.content}}></div>
            :
              <div className="card-body">
                <ProgressBar mode="indeterminate" style={{ height: '2em' }}></ProgressBar>
              </div>
            }
          </div>
        })}
      </div>
    </>
  }

}