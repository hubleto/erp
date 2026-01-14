import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';

interface TableFoldersProps extends TableExtendedProps {}
interface TableFoldersState extends TableExtendedState {}

export default class TableFolders extends TableExtended<TableFoldersProps, TableFoldersState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Documents/Models/Folder',
  }

  props: TableFoldersProps;
  state: TableFoldersState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\TableFolders';

  constructor(props: TableFoldersProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableFoldersProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right';
    return params;
  }

  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/documents/' + (id > 0 ? id : 'add'));
  }
}