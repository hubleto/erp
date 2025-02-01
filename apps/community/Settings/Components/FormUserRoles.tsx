import React, { Component, createRef, RefObject } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import request from 'adios/Request';

const formRef = createRef();

interface FormUserRolesProps extends FormProps {}

interface FormUserRolesState extends FormState {
  sortedAllPermissions: any,
  rolePermissions: any,
  dataLoading: boolean,
  dataCalled: boolean,
  selectedGroup: string,
}

export default class FormUserRoles<P, S> extends Form<FormUserRolesProps,FormUserRolesState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'HubletoApp/Community/Settings/Models/UserRole',
  };

  props: FormUserRolesProps;
  state: FormUserRolesState;

  translationContext: string = 'mod.core.settings.formUserRole';

  constructor(props: FormUserRolesProps) {
    super(props);
    this.state = {
      ...this.getStateFromProps(props),
      sortedAllPermissions: null,
      rolePermissions: null,
      dataLoading: true,
      dataCalled: false,
      selectedGroup: "",
    };
  }

  getStateFromProps(props: FormUserRolesProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  saveRecord(): void {
    this.setState({ dataLoading: true, isInlineEditing: false } as FormUserRolesState);

    if (formRef.current) {
      const formData = new FormData(formRef.current);
      const formValues = Object.fromEntries(formData.entries());

      request.get(
        'settings/save-permissions',
        {
          roleId: this.state.record.id,
          permissions: formValues,
          roleTitle: this.state.record.role,
          grantAll: this.state.record.grant_all,
        },
        (data: any) => {
          if (data.status == "success") {
            this.loadRecord();
            this.props.parentTable.loadData();
            this.getPermissions();
          }
        }
      );
    }
  }

  renderSaveButton(): JSX.Element {
    let id = this.state.id ? this.state.id : 0;

    return <>
      {this.state.description?.permissions?.canUpdate ? <button
        onClick={() => this.saveRecord()}
        className={
          "btn btn-add "
          + (id <= 0 && this.state.description?.permissions?.canCreate || id > 0 && this.state.description?.permissions?.canUpdate ? "d-block" : "d-none")
        }
      >
        <span className="icon"><i className="fas fa-save"></i></span>
        <span className="text">
          {this.state.description?.ui?.saveButtonText ?? this.translate("Save")}
        </span>
      </button> : null}
    </>;
  }

  renderHeaderLeft(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton()}
    </>;
  }

  renderHeaderRight(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderDeleteButton() : null}
      {this.props.showInModal ? this.renderCloseButton() : null}
    </>;
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return(
        <>
          <h2>
            {'New User Role'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.role
              ? this.state.record.role
              : '[Undefined Name]'}
          </h2>
        </>
      );
    }
  }

  getPermissions(): any {
    this.setState({ dataLoading: true } as FormUserRolesState);
    request.get(
      'settings/get-permissions',
      {roleId: this.state.record.id},
      (data: any) => {
        if (data.status == "success") {
          this.setState({
            sortedAllPermissions: data.sortedAllPermissions,
            rolePermissions: data.rolePermissions,
            dataLoading: false,
          } as FormUserRolesState)
        }
      }
    );
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    if (this.state.dataCalled == false) {
      this.setState({ dataCalled: true } as FormUserRolesState)
      this.getPermissions();
    }

    return (
      <>
        {this.state.dataLoading ? <>Loading</> :
          <>
            <div className='card'>
              <div className='card-body flex flex-row justify-around'>
                {this.inputWrapper("role")}
                {this.inputWrapper("grant_all")}
              </div>
            </div>
            {R.grant_all == false ?
              <form ref={formRef}>
                <div className='card'>
                  <div className='card-header'>
                    <p className='text-bold'>Permissions</p>
                  </div>
                  <div className='card-body'>
                    {Object.entries(this.state.sortedAllPermissions).map(([app, groups]) => (
                      <div className='card' >
                        <div className='card-header cursor-pointer'
                          onClick={() => {
                            if (this.state.selectedGroup == app) {
                              this.setState({ selectedGroup: "" } as FormUserRolesState);
                            } else this.setState({ selectedGroup: app } as FormUserRolesState)}
                          }
                        >
                          <p>{app}</p>
                          <span className='icon'><i className='fas fa-chevron-down'></i></span>
                        </div>
                        <div className={`card-body ${this.state.selectedGroup == app ? "block" : "hidden"}`}>
                          {Object.entries(groups).map(([key, group]) => (
                            <div className='card card-body mb-2'>
                              <p className='font-bold'>{key}</p>
                              {Object.entries(group).map(([key, permission]) => (
                                <div className='flex flex-row justify-between my-1'>
                                  <label htmlFor={`permission_id_${permission.id}`}>{permission.alias ?? permission.permission}</label>
                                  <input
                                    id={`permission_id_${permission.id}`}
                                    type='checkbox'
                                    readOnly={this.state.isInlineEditing}
                                    onClick={() => this.setState({ isInlineEditing: true })}
                                    defaultChecked={this.state.rolePermissions.includes(permission.id) ? true : null}
                                    name={permission.id}
                                    value={permission.id}
                                  />
                                </div>
                              ))}
                            </div>
                          ))}
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </form>
            : <></>}
          </>

        }

      </>
    )
  }
}
