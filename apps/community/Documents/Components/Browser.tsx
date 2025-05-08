import React, { Component } from 'react'
import request from "adios/Request";
import Table, { TableProps, TableState } from 'adios/Table';
import { FormProps } from 'adios/Form';
import FormDocument from './FormDocument';
import { ProgressBar } from 'primereact/progressbar';

interface BrowserProps extends TableProps {
  folderUid?: string,
  documentUid?: string,
  path?: Array<any>,
}
interface BrowserState extends TableState {
  folderUid: string,
  documentUid?: string,
  folderContent: any,
  path: Array<any>,
}

export default class Browser extends Table<BrowserProps, BrowserState> {
  static defaultProps = {
    ...Table.defaultProps,
    itemsPerPage: 15,
    formUseModalSimple: true,
    orderBy: {
      field: "id",
      direction: "desc"
    },
    model: 'HubletoApp/Community/Documents/Models/Document',
  }

  props: BrowserProps;
  state: BrowserState;

  translationContext: string = 'HubletoApp\\Community\\Documents\\Loader::Components\\Browser';

  constructor(props: BrowserProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      folderUid: this.props.folderUid ? this.props.folderUid : '_ROOT_',
      documentUid: this.props.documentUid,
      folderContent: null,
      path: this.props.path ?? [],
    };
  }

  getStateFromProps(props: BrowserProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  loadData() {
    this.setState({loadingData: true}, () => {
      request.get(
        '',
        {
          route: 'documents/api/get-folder-content',
          folderUid: this.state.folderUid,
          fulltextSearch: this.state.fulltextSearch,
        },
        (folderContent: any) => {
          this.setState({
            loadingData: false,
            folderContent: folderContent,
          } as BrowserState);
        }
      );
    });
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right';
    return params;
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    return <FormDocument {...formProps}/>;
  }

  changeFolder(newFolderUid: string, newPath: Array<string>) {
    this.setState({
      recordId: 0,
      folderUid: newFolderUid,
      path: newPath,
    } as BrowserState, () => { this.loadData(); });
  }

  render(): JSX.Element {

    if (!this.state.folderContent) {
      return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
    }

    return <>
      <div className="flex gap-2">
        <button
          className="btn btn-info text-xl"
          onClick={() => { this.changeFolder('_ROOT_', []); }}
        >
          <span className="icon"><i className="fas fa-home"></i></span>
        </button>
        {this.state.path.map((item, index) => {
          return <button
            key={index}
            className="btn btn-info text-xl"
            onClick={() => {
              let newPath: Array<any> = [];
              for (let i = 0; i <= index; i++) newPath.push(this.state.path[i]);
              this.changeFolder(item.uid, newPath);
            }}
          >
            <span className="text">{item.name}</span>
          </button>;
        })}
      </div>
      <div className="flex gap-2 mt-2">
        {/* {this.state.folderUid == '_ROOT_' ? null : <button
          className="btn btn-square btn-transparent btn-yellow-outline w-32"
          onClick={() => {
            let newFolderUid = this.state.folderContent.folder.PARENT_FOLDER.uid;
            let newPath = this.state.path;
            newPath.pop();
            newPath.push(newFolderUid);
              this.changeFolder(newFolderUid, newPath);
          }}
        >
          <span className="icon"><i className="fas fa-folder"></i></span>
          <span className="text">..</span>
        </button>} */}
        {this.state.folderContent.subFolders ? this.state.folderContent.subFolders.map((item, index) => {
          return <button
            key={index}
            className="btn btn-square btn-transparent btn-yellow-outline w-32"
            onClick={() => {
              let newFolderUid = item.uid;
              let newPath = this.state.path;
              newPath.push(item);
              this.changeFolder(newFolderUid, newPath);
            }}
          >
            <span className="icon"><i className="fas fa-folder"></i></span>
            <span className="text">{item.name ?? ''}</span>
          </button>
        }) : null}
        {this.state.folderContent.documents ? this.state.folderContent.documents.map((item, index) => {
          return <button
            key={index}
            className="btn btn-square btn-transparent"
            onClick={() => {
              this.setState({ recordId: item.id });
            }}
          >
            <span className="icon"><i className="fas fa-file"></i></span>
            <span className="text">{item.name ?? ''}</span>
          </button>
        }) : null}
      </div>
      {this.renderFormModal()}
    </>
  }
}