import React, { Component } from 'react'
import request from "@hubleto/react-ui/core/Request";
import Table, { TableProps, TableState } from '@hubleto/react-ui/core/Table';
import Form, { FormProps } from '@hubleto/react-ui/core/Form';
import FormDocument from './FormDocument';
import { ProgressBar } from 'primereact/progressbar';
import ModalForm from "@hubleto/react-ui/core/ModalForm";
import Lookup from '@hubleto/react-ui/core/Inputs/Lookup';

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
  selectedFolders: Array<number>,
  selectedDocuments: Array<number>,
  deletingRecord?: boolean,
  deleteButtonDisabled?: boolean,
  showBulkMove?: boolean,
}

export default class Browser extends Table<BrowserProps, BrowserState> {
  static defaultProps = {
    ...Table.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Documents/Models/Document',
  }

  props: BrowserProps;
  state: BrowserState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\Browser';

  refFolderPropertiesModal: any;
  refBulkMoveFolderLookup: any;

  constructor(props: BrowserProps) {
    super(props);
    this.refFolderPropertiesModal = React.createRef();
    this.refBulkMoveFolderLookup = React.createRef();
    this.state = {
      ...this.getStateFromProps(props),
      folderUid: this.props.folderUid ? this.props.folderUid : '_ROOT_',
      documentUid: this.props.documentUid,
      folderContent: null,
      path: this.props.path ?? [],
      showFolderProperties: 0,
      selectedFolders: [],
      selectedDocuments: [],
      deletingRecord: false,
      deleteButtonDisabled: false,
      showBulkMove: false,
    };
  }

  getStateFromProps(props: BrowserProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  componentDidMount() {
    super.componentDidMount();
    window.addEventListener('popstate', this.handlePopState);
  }

  componentWillUnmount() {
    window.removeEventListener('popstate', this.handlePopState);
  }

  handlePopState = (event: any) => {
    if (event.state && event.state.folderUid) {
      this.setState(event.state, () => { this.loadData(); });
    } else {
      this.setState({
        recordId: 0,
        folderUid: '_ROOT_',
        path: [],
        showFolderProperties: 0,
      } as BrowserState, () => { this.loadData(); });
    }
  }

  loadData() {
    this.setState({
      loadingData: true,
      selectedFolders: [],
      selectedDocuments: [],
      deletingRecord: false,
      showBulkMove: false,
    }, () => {
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
    params.type = 'right wider';
    return params;
  }

  renderForm(): JSX.Element {
    let formProps: FormProps = this.getFormProps();
    formProps.customEndpointParams = {idFolder: this.state.folderContent.folder.id};
    return <FormDocument {...formProps}/>;
  }

  changeFolder(newFolderUid: string, newPath: Array<string>) {
    const newState = {
      recordId: 0,
      folderUid: newFolderUid,
      path: newPath,
      showFolderProperties: 0,
    };
    
    window.history.pushState(newState, "", '?folderUid=' + newFolderUid);
    this.setState(newState as BrowserState, () => { this.loadData(); });
  }

  createSubFolder() {
    this.setState({
      showFolderProperties: -1
    });
  }

  toggleSelection(id: number, type: 'selectedFolders' | 'selectedDocuments') {
    const sel = this.state[type].includes(id)
      ? this.state[type].filter(i => i !== id)
      : [...this.state[type], id];
    this.setState({ [type]: sel } as any);
  }

  bulkDelete() {
    this.setState({deletingRecord: false, deleteButtonDisabled: false });
    const items = [
      ...this.state.selectedFolders.map(id => ({ id, model: 'Hubleto/App/Community/Documents/Models/Folder' })),
      ...this.state.selectedDocuments.map(id => ({ id, model: 'Hubleto/App/Community/Documents/Models/Document' }))
    ];
        const promises = items.map(item => new Promise(resolve => {
      request.delete('api/record/delete', item, resolve, resolve);
    }));
    Promise.all(promises).then(() => this.loadData());
  }

  bulkMove() {
    const idFolder = Number(this.refBulkMoveFolderLookup.current?.state?.value ?? 0);
    const promises = this.state.selectedDocuments.map(id => new Promise(resolve => {
      const doc = this.state.folderContent.documents.find((d: any) => d.id === id);
      request.post( 'api/record/save', { model: 'Hubleto/App/Community/Documents/Models/Document', record: { ...doc, id_folder: idFolder  }, },
        {},
        resolve,
        resolve,
      );
    }));
    Promise.all(promises).then(() => this.loadData());
  }

  render(): JSX.Element {

    if (!this.state.folderContent) {
      return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
    }

    return <>
      <div className="flex gap-2">
        <button
          className="btn btn-info text-xl dark:bg-blue-900 dark:text-blue-100 dark:border-blue-900"
          onClick={() => { this.changeFolder('_ROOT_', []); }}
        >
          <span className="icon"><i className="fas fa-home"></i></span>
        </button>
        {this.state.path.map((item, index) => {
          const isLast = index == this.state.path.length - 1;

          return <button
            key={index}
            className={"btn text-xl " + (isLast ? "btn-info dark:bg-blue-900 dark:text-blue-100 dark:border-blue-900" : "btn-cancel dark:bg-gray-700 dark:text-gray-200")}
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
            <span className="text flex items-center gap-1">
              <span className="truncate max-w-[150px]">{item.name}</span>
              {isLast ? <i className='fa fa-chevron-down'></i> : <></>}
            </span>
          </button>;
        })}
        <button
          className="btn btn-transparent text-xl"
          onClick={() => { this.createSubFolder(); }}
        >
          <span className="icon"><i className="fas fa-plus"></i></span>
          <span className="text">{this.translate('Add folder')}</span>
        </button>
        {this.state.selectedDocuments.length > 0 ? (
          <>
            <button
              onClick={() => {
                this.setState({ showBulkMove: !this.state.showBulkMove });
              }}
              className="btn btn-transparent text-xl"
            >
              <span className="icon"><i className="fas fa-folder-open"></i></span>
              <span className="text text-nowrap">
                {this.translate("Move")}
              </span>
            </button>
            {this.state.showBulkMove ? (
              <div className="flex gap-2 items-center">
                <Lookup
                  ref={this.refBulkMoveFolderLookup}
                  model='Hubleto/App/Community/Documents/Models/Folder'
                  value={this.state.folderContent.folder.id}
                  uiStyle='select'
                ></Lookup>
                <button
                  className="btn btn-info text-nowrap"
                  onClick={() => { this.bulkMove(); }}
                >
                  <span className="text">{this.translate('Move selected')}</span>
                </button>
              </div>
            ) : null}
          </>
        ) : null}
        {this.state.selectedFolders.length > 0 || this.state.selectedDocuments.length > 0 ? (
          <button
            onClick={() => {
              if (!this.state.deleteButtonDisabled) {
                if (this.state.deletingRecord) this.bulkDelete();
                else {
                  this.setState({deletingRecord: true, deleteButtonDisabled: true});
                  setTimeout(() => this.setState({deleteButtonDisabled: false}), 1000);
                }
              }
            }}
            className={ "btn text-xl " + (this.state.deletingRecord ? "font-bold" : "") + " " + (this.state.deleteButtonDisabled ? "btn-light" : "btn-delete")}
          >
            <span className="icon"><i className="fas fa-trash-alt"></i></span>
            <span className="text text-nowrap">
              {this.state.deletingRecord ?
                this.translate("Confirm delete")
                : this.translate("Delete")
              }
            </span>
          </button>
        ) : null}
      </div>
      <div className="flex gap-2 mt-2">
        {this.state.folderContent.subFolders ? this.state.folderContent.subFolders.map((item, index) => {
          const isSelected = this.state.selectedFolders.includes(item.id);
          return <button
            key={index}
            className={"relative btn btn-square w-32 " + (isSelected ? "btn-primary dark:bg-blue-900" : "btn-light")}
            onClick={(e) => {
              let newFolderUid = item.uid;
              let newPath = this.state.path;
              newPath.push(item);
              this.changeFolder(newFolderUid, newPath);
            }}
          >
            <input 
              type="checkbox" 
              className="absolute top-2 left-2 cursor-pointer w-4 h-4" 
              checked={isSelected}
              onChange={() => this.toggleSelection(item.id, 'selectedFolders')}
              onClick={(e) => e.stopPropagation()}
            />
            <span className="icon"><i className="fas fa-folder"></i></span>
            <div className="text line-clamp-2 w-full break-words">{item.name ?? ''}</div>
          </button>
        }) : null}
        {this.state.folderContent.documents ? this.state.folderContent.documents.map((item, index) => {
          const isSelected = this.state.selectedDocuments.includes(item.id);
          return <button
            key={index}
            className={"relative btn btn-square w-32 " + (isSelected ? "btn-primary dark:bg-blue-900" : "btn-primary-outline dark:bg-transparent")}
            onClick={(e) => {
              this.setState({ recordId: item.id });
            }}
          >
            <input 
              type="checkbox" 
              className="absolute top-2 left-2 cursor-pointer w-4 h-4" 
              checked={isSelected}
              onChange={() => this.toggleSelection(item.id, 'selectedDocuments')}
              onClick={(e) => e.stopPropagation()}
            />
            <span className="icon"><i className="fas fa-file"></i></span>
            <div className="text line-clamp-2 w-full break-words">{item.name ?? ''}</div>
            {item.is_public ?
              <div className="text-xs text-yellow-800 p-1">{this.translate('Public')}</div>
            : null}
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
          ref={this.refFolderPropertiesModal}
          uid='create_sub_folder_modal'
          isOpen={true}
          type='right'
          form={{}}
          onClose={() => { this.setState({showFolderProperties: 0}); }}
        >
          <Form
            modal={this.refFolderPropertiesModal}
            uid='create_sub_folder_form'
            model='Hubleto/App/Community/Documents/Models/Folder'
            isInlineEditing={true}
            customEndpointParams={{idParentFolder: this.state.folderContent.folder.id, noSelfParent: true}}
            id={this.state.showFolderProperties}
            onSaveCallback={(form, saveResponse, customSaveOptions) => {
              //if the folder is being moved to another parent folder
              if (saveResponse.originalRecord.id_parent_folder != saveResponse.savedRecord.id_parent_folder) {
                this.changeFolder("_ROOT_", []);
              } else {
                this.loadData();
              }

              this.setState({showFolderProperties: 0});
            }}
            onClose={() => { this.setState({showFolderProperties: 0}); }}
            onDeleteCallback={() => {
              const secondLastIndex = this.state.path.length - 2;
              let item = this.state.path[secondLastIndex]
              let newPath: Array<any> = [];
              for (let i = 0; i <= secondLastIndex; i++) newPath.push(this.state.path[i]);
              this.changeFolder(item.uid, newPath);
              this.setState({showFolderProperties: 0});
            }}
          />
        </ModalForm>
      : null}
    </>
  }
}