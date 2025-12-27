import React, { Component } from 'react'
import HubletoTable, { HubletoTableProps, HubletoTableState } from '@hubleto/react-ui/ext/HubletoTable';

interface TableFoldersProps extends HubletoTableProps {}
interface TableFoldersState extends HubletoTableState {}

export default class TableFolders extends HubletoTable<TableFoldersProps, TableFoldersState> {
  static defaultProps = {
    ...HubletoTable.defaultProps,
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
    window.history.pushState({}, "", globalThis.main.config.projectUrl + '/documents/' + (id > 0 ? id : 'add'));
  }
}