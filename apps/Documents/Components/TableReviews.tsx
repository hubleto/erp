import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import FormReview, { FormReviewProps } from './FormReview';

interface TableReviewsProps extends TableExtendedProps {
  idDocument?: number,
  idVersion?: number,
}
interface TableReviewsState extends TableExtendedState {}

export default class TableReviews extends TableExtended<TableReviewsProps, TableReviewsState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Documents/Models/Review',
  }

  props: TableReviewsProps;
  state: TableReviewsState;

  translationContext: string = 'Hubleto\\App\\Community\\Documents\\Loader';
  translationContextInner: string = 'Components\\TableReviews';

  constructor(props: TableReviewsProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: TableReviewsProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wider';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idDocument: this.props.idDocument,
      idVersion: this.props.idVersion,
    }
  }
  setRecordFormUrl(id: number) {
    window.history.pushState({}, "", globalThis.hubleto.config.projectUrl + '/documents/' + (id > 0 ? id : 'add'));
  }

  renderCell(columnName: string, column: any, data: any, options: any) {
    if (columnName == "hyperlink") {
      return <>
        {data[columnName] && data[columnName].length > 28 ? data[columnName].substring(0, 28) + '...' : data[columnName]}
        <a
          href={data[columnName]}
          target='_blank'
          onClick={(e) => { e.stopPropagation(); }}
          className="btn btn-transparent"
        >
          <span className="icon"><i className="fa-solid fa-up-right-from-square"></i></span>
        </a>
      </>;
    } else {
      return super.renderCell(columnName, column, data, options);
    }
  }

  renderForm(): JSX.Element {
    let formProps: FormReviewProps = this.getFormProps();
    formProps.customEndpointParams.idDocument = this.props.idDocument;
    formProps.customEndpointParams.idVersion = this.props.idVersion;
    if (!formProps.description) formProps.description = {};
    formProps.description.defaultValues = { id_document: this.props.idDocument, id_version: this.props.idVersion };
    return <FormReview {...formProps}/>;
  }
}