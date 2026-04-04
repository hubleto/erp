import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';

interface TableReviewResultsProps extends TableExtendedProps {}
interface TableReviewResultsState extends TableExtendedState {}

export default class TableReviewResults extends TableExtended<TableReviewResultsProps, TableReviewResultsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Documents/Models/ReviewResult',
  }

  props: TableReviewResultsProps;
  state: TableReviewResultsState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\TableReviewResults';

  constructor(props: TableReviewResultsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableReviewResultsProps) {
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
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/documents/review-results/' + (id > 0 ? id : 'add'));
  }
}