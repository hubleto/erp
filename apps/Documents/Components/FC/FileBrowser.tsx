import React, { Component, useState, useEffect, useCallback} from 'react'
import request from "@hubleto/react-ui/core/Request";
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import FormFile from './FormFile';
import Spinner from '@hubleto/react-ui/components/fc/Spinner';
import Modal from '@hubleto/react-ui/components/fc/Modal';
import LookupInput from '@hubleto/react-ui/components/fc/Inputs/Lookup';
import Translator from '@hubleto/react-ui/core/Translator';
import FormDocument from './FormDocument';

interface FileBrowserProps extends TableProps {
  folderUid?: string,
  fileUid?: string,
  path?: Array<any>,
}

const componentName = 'FileBrowser'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);


const FileBrowser = (props: FileBrowserProps) => {

  //special document folder that contains files from the Document app
  const documentFolder = "document_folder";
  const documentModel = "Hubleto/App/Community/Documents/Models/Document";
  const fileModel = "Hubleto/App/Community/Documents/Models/File";

  const refFolderPropertiesModal = React.createRef();
  const refBulkMoveFolderLookup = React.createRef();

  //useState()
  const [folderUid, setFolderUid] = useState(props.folderUid ? props.folderUid : '_ROOT_');
  //const [fileUid, setFileUid] = useState(props.fileUid);
  const [folderContent, setFolderContent] = useState(null);
  const [path, setPath] = useState(props.path ?? []);
  const [showFolderProperties, setShowFolderProperties] = useState(0);
  const [selectedFolders, setSelectedFolders] = useState([]);
  const [selectedFiles, setSelectedFiles] = useState([]);
  const [deletingRecord, setDeletingRecord] = useState(false);
  const [deleteButtonDisabled, setDeleteButtonDisabled] = useState(false);
  const [showBulkMove, setShowBulkMove] = useState(false);
  const [recordId, setRecordId] = useState(0);
  const [model, setModel] = useState(null);

  useEffect(() => {
    setRecordId(0);
    setFolderUid('_ROOT_');
    setPath([]);
    setShowFolderProperties(0);
    loadData();
  }, [])

  useEffect(() => {
    loadData()
  }, [folderUid])

  useEffect(() => {
    if (recordId > 0) {
      renderForm(recordId)
    }
  }, [recordId])

  const loadData = (): void => {
    setSelectedFolders([]);
    setSelectedFiles([]);
    setDeletingRecord(false);
    setShowBulkMove(false);
    request.get(
      '',
      {
        route: 'documents/api/get-folder-content',
        folderUid: folderUid
      },
      (folderContent: any) => {
        setFolderContent(folderContent);
      }
    );
  }

  // getFormModalProps(): any {
  //   let params = super.getFormModalProps();
  //   params.type = 'right wider';
  //   return params;
  // }

  const renderForm = (recordId: number): React.JSX.Element => {
    var props = {
      id: recordId,
      onClose: () => {setRecordId(0);},
      onAfterDeleteRecord: () => {loadData()},
      onAfterSaveRecord: () => {loadData()},
    };
    return <Modal
      uid='create_sub_folder_modal'
      isOpen={true}
      type='right'
    >
      {model === documentModel ?
        <FormDocument {...props}></FormDocument>
      :
        <FormFile {...props}></FormFile>
      }
    </Modal>
    ;
  }

  const changeFolder = (newFolderUid: string, newPath: Array<string>) => {
    const newState = {
      recordId: 0,
      folderUid: newFolderUid,
      path: newPath,
      showFolderProperties: 0,
    };

    window.history.pushState(newState, "", '?folderUid=' + newFolderUid);
    setRecordId(0);
    setFolderUid(newFolderUid);
    setPath(newPath);
    setShowFolderProperties(0);
  }

  const createSubFolder = () => {
    setShowFolderProperties(-1);
  }

  const toggleSelection = (id: number, type: 'selectedFolders' | 'selectedFiles') => {
    const select =
    type === 'selectedFolders'
      ? setSelectedFolders
      : setSelectedFiles;

    select(prev =>
      prev.includes(id)
        ? prev.filter(i => i !== id)
        : [...prev, id]
    );
  }

  const bulkDelete = () => {
    setDeletingRecord(false);
    setDeleteButtonDisabled(false);
    const fileDeteletionModel = folderUid === documentFolder ? documentModel : fileModel;
    const items = [
      ...selectedFolders.map(id => ({ id, model: 'Hubleto/App/Community/Documents/Models/Folder' })),
      ...selectedFiles.map(id => ({ id, model: fileDeteletionModel }))
    ];
    const promises = items.map(item => new Promise(resolve => {
      request.delete('api/record/delete', item, resolve, resolve);
    }));
    Promise.all(promises).then(() => loadData());
  }

  const bulkMove = () => {
    const idFolder = Number(refBulkMoveFolderLookup.current?.state?.value ?? 0);
    const promises = selectedFiles.map(id => new Promise(resolve => {
      const doc = folderContent.files.find((d: any) => d.id === id);
      request.post( 'api/record/save', { model: 'Hubleto/App/Community/Documents/Models/File', record: { ...doc, id_folder: idFolder  }, },
        {},
        resolve,
        resolve,
      );
    }));
    Promise.all(promises).then(() => loadData());
  }

  const render = (): React.JSX.Element => {

    if (!folderContent) {
      return <Spinner></Spinner>;
    }

    return <>
      <div className="flex gap-2">
        <button
          className="btn btn-info text-xl dark:bg-blue-900 dark:text-blue-100 dark:border-blue-900"
          onClick={() => { changeFolder('_ROOT_', []); }}
        >
          <span className="icon"><i className="fas fa-home"></i></span>
        </button>

        {/* folder breadcrubs */}
        {path.map((item, index) => {
          const isLast = index == path.length - 1;

          return <button
            key={index}
            className={"btn text-xl " + (isLast ? "btn-info dark:bg-blue-900 dark:text-blue-100 dark:border-blue-900" : "btn-cancel dark:bg-gray-700 dark:text-gray-200")}
            onClick={() => {
              if (isLast) {
                setShowFolderProperties(folderContent.folder.id);
              } else {
                let newPath: Array<any> = [];
                for (let i = 0; i <= index; i++) newPath.push(path[i]);
                changeFolder(item.uid, newPath);
              }
            }}
          >
            <span className="text flex items-center gap-1">
              <span className="truncate max-w-[150px]">{item.name}</span>
              {isLast ? <i className='fa fa-chevron-down'></i> : <></>}
            </span>
          </button>;
        })}
        {folderUid != documentFolder ?
          <button
            className="btn btn-transparent text-xl"
            onClick={() => { createSubFolder(); }}
          >
            <span className="icon"><i className="fas fa-plus"></i></span>
            <span className="text">{T.translate('Add folder')}</span>
          </button>
        : null}

        {/* bulk options for files */}
        {selectedFiles.length > 0 ? (
          <>
            {folderUid != documentFolder ?
              <button
                onClick={() => {
                  setShowBulkMove(!showBulkMove)
                }}
                className="btn btn-transparent text-xl"
              >
                <span className="icon"><i className="fas fa-folder-open"></i></span>
                <span className="text text-nowrap">
                  {T.translate("Move")}
                </span>
              </button>
            : null}
            {showBulkMove ? (
              <div className="flex gap-2 items-center">
                <LookupInput
                  ref={refBulkMoveFolderLookup}
                  model='Hubleto/App/Community/Documents/Models/Folder'
                  value={folderContent.folder.id}
                  uiStyle='select'
                ></LookupInput>
                <button
                  className="btn btn-info text-nowrap"
                  onClick={() => { bulkMove(); }}
                >
                  <span className="text">{T.translate('Move selected')}</span>
                </button>
              </div>
            ) : null}
          </>
        ) : null}

        {/* bulk options for folders */}
        {selectedFolders.length > 0 || selectedFiles.length > 0 ? (
          <button
            onClick={() => {
              if (!deleteButtonDisabled) {
                if (deletingRecord) bulkDelete();
                else {
                  setDeletingRecord(true);
                  setDeleteButtonDisabled(true);
                  setTimeout(() => setDeleteButtonDisabled(false), 1000);
                }
              }
            }}
            className={ "btn text-xl " + (deletingRecord ? "font-bold" : "") + " " + (deleteButtonDisabled ? "btn-light" : "btn-delete")}
          >
            <span className="icon"><i className="fas fa-trash-alt"></i></span>
            <span className="text text-nowrap">
              {deletingRecord ?
                T.translate("Confirm delete")
                : T.translate("Delete")
              }
            </span>
          </button>
        ) : null}
      </div>

      <div className="flex gap-2 mt-2">
        {/* folders */}
        {folderContent.subFolders ? folderContent.subFolders.map((item, index) => {
          const isSelected = selectedFolders.includes(item.id);
          return <button
            key={index}
            className={"relative btn btn-square w-32 " + (isSelected ? "btn-primary dark:bg-blue-900" : "btn-light")}
            onClick={(e) => {
              let newFolderUid = item.uid;
              let newPath = path;

              newPath.push(item);
              changeFolder(newFolderUid, newPath);
            }}
          >
            <input
              type="checkbox"
              className="absolute top-2 left-2 cursor-pointer w-4 h-4"
              checked={isSelected}
              onChange={() => toggleSelection(item.id, 'selectedFolders')}
              onClick={(e) => e.stopPropagation()}
            />
            <span className="icon"><i className="fas fa-folder"></i></span>
            <div className="text line-clamp-2 w-full break-words">{item.name ?? ''}</div>
          </button>
        }) : null}
        {folderUid === "_ROOT_" ?
          <button
            key={documentFolder}
            className={"relative btn btn-square w-32 btn-light"}
            onClick={(e) => {
              let newFolderUid = documentFolder;
              let newPath = path;
              newPath.push({
                id_parent_folder: 1,
                name:"Documents",
                uid: newFolderUid
              });
              changeFolder(newFolderUid, newPath);
            }}
          >
            <span className="icon"><i className="fas fa-folder"></i></span>
            <div className="text line-clamp-2 w-full break-words">Documents</div>
          </button>
        : <></>}

        {/* files */}
        {folderContent.files ? folderContent.files.map((item, index) => {
          const isSelected = selectedFiles.includes(item.id);
          return <button
            key={index}
            className={"relative btn btn-square w-32 " + (isSelected ? "btn-primary dark:bg-blue-900" : "btn-primary-outline dark:bg-transparent")}
            onClick={(e) => {
              if (folderUid === documentFolder) {
                setModel(documentModel);
              } else {
                setModel(fileModel);
              }
              setRecordId(item.id);
            }}
          >
            <input
              type="checkbox"
              className="absolute top-2 left-2 cursor-pointer w-4 h-4"
              checked={isSelected}
              onChange={() => toggleSelection(item.id, 'selectedFiles')}
              onClick={(e) => e.stopPropagation()}
            />
            <span className="icon"><i className="fas fa-file"></i></span>
            <div className="text line-clamp-2 w-full break-words">{item.name ?? ''}</div>
            {item.is_public ?
              <div className="text-xs text-yellow-800 p-1">{T.translate('Public')}</div>
            : null}
          </button>
        }) : null}
        <button
          className="btn btn-square btn-transparent"
          onClick={() => {
            if (folderUid === documentFolder) {
              setModel(documentModel);
            } else {
              setModel(fileModel)
            }
            setRecordId(-1);
          }}
        >
          <span className="icon"><i className="fas fa-plus"></i></span>
          <span className="text">{T.translate('Add file')}</span>
        </button>
      </div>
      {recordId ?
       renderForm(recordId)
      : <></>}
      {showFolderProperties ?
        <Modal
          uid='create_sub_folder_modal'
          isOpen={true}
          type='right'
        >
          <Form
            modal={refFolderPropertiesModal}
            uid='create_sub_folder_form'
            model='Hubleto/App/Community/Documents/Models/Folder'
            // customEndpointParams={{idParentFolder: folderContent.folder.id, noSelfParent: true}}
            id={showFolderProperties}
            onAfterSaveRecord={(form, saveResponse, customSaveOptions) => {
              //if the folder is being moved to another parent folder
              if (saveResponse.originalRecord.id_parent_folder != saveResponse.savedRecord.id_parent_folder) {
                changeFolder("_ROOT_", []);
              } else {
                loadData();
              }

              setShowFolderProperties(0)
            }}
            onClose={() => { setShowFolderProperties(0)}}
            onAfterDeleteRecord={() => {
              const secondLastIndex = path.length - 2;
              let item = path[secondLastIndex]
              let newPath: Array<any> = [];
              for (let i = 0; i <= secondLastIndex; i++) newPath.push(path[i]);
              changeFolder(item.uid, newPath);
              setShowFolderProperties(0);
            }}
          />
        </Modal>
      : null}
    </>
  }
 return render();
}

export default FileBrowser;