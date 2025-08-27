import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import Form, { FormProps } from '@hubleto/react-ui/core/Form';
import FormDocument from './FormDocument';
import { ProgressBar } from 'primereact/progressbar';
import ModalForm from "@hubleto/react-ui/core/ModalForm";

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
  showFolderProperties: number,
}

export default class Browser extends Table<BrowserProps, BrowserState> {
  static defaultProps = {
    ...Table.defaultProps,
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
      showFolderProperties: 0,
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
    formProps.customEndpointParams = {idFolder: this.state.folderContent.folder.id};
    return <FormDocument {...formProps}/>;
  }

  changeFolder(newFolderUid: string, newPath: Array<string>) {
    this.setState({
      recordId: 0,
      folderUid: newFolderUid,
      path: newPath,
      showFolderProperties: 0,
    } as BrowserState, () => { this.loadData(); });
  }

  createSubFolder() {
    this.setState({
      showFolderProperties: -1
    });
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
          const isLast = index == this.state.path.length - 1;

          return <button
            key={index}
            className={"btn text-xl " + (isLast ? "btn-info" : "btn-cancel")}
            onClick={() => {
              if (isLast) {
                this.setState({showFolderProperties: this.state.folderContent.folder.id});
              } else {
                let newPath: Array<any> = [];
                for (let i = 0; i <= index; i++) newPath.push(this.state.path[i]);
                this.changeFolder(item.uid, newPath);
              }
            }}
          >
            <span className="text">{item.name}</span>
          </button>;
        })}
        <button
          className="btn btn-transparent text-xl"
          onClick={() => { this.createSubFolder(); }}
        >
          <span className="icon"><i className="fas fa-plus"></i></span>
          <span className="text">{this.translate('Add folder')}</span>
        </button>
      </div>
      <div className="flex gap-2 mt-2">
        {this.state.folderContent.subFolders ? this.state.folderContent.subFolders.map((item, index) => {
          return <button
            key={index}
            className="btn btn-square btn-light w-32"
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
            className="btn btn-square btn-primary-outline"
            onClick={() => {
              this.setState({ recordId: item.id });
            }}
          >
            <span className="icon"><i className="fas fa-file"></i></span>
            <span className="text">{item.name ?? ''}</span>
          </button>
        }) : null}
        <button
          className="btn btn-square btn-transparent"
          onClick={() => {
            this.setState({ recordId: -1 });
          }}
        >
          <span className="icon"><i className="fas fa-plus"></i></span>
          <span className="text">{this.translate('Add document')}</span>
        </button>
      </div>
      {this.renderFormModal()}
      {this.state.showFolderProperties ?
        <ModalForm
          uid='create_sub_folder_modal'
          isOpen={true}
          type='right'
        >
          <Form
            uid='create_sub_folder_form'
            model='HubletoApp/Community/Documents/Models/Folder'
            customEndpointParams={{idParentFolder: this.state.folderContent.folder.id}}
            id={this.state.showFolderProperties}
            showInModal={true}
            showInModalSimple={true}
            onClose={() => { this.setState({showFolderProperties: 0}); }}
          />
        </ModalForm>
      : null}
    </>
  }
}