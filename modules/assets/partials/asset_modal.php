  <?php
    /** @var array $categories */
    /** @var array $employees */ ?>

  <div
      class="modal fade"
      id="assetModal"
      tabindex="-1">

      <div class="modal-dialog modal-xl">

          <div class="modal-content">

              <form
                  method="POST"
                  action="../actions/assets_create.php"
                  novalidate>

                  <input
                      type="hidden"
                      name="csrf_token"
                      value="<?= csrf_token() ?>">

                  <div class="modal-header">

                      <h5 class="modal-title">

                          Dodaj inventar

                      </h5>

                      <button
                          type="button"
                          class="btn-close"
                          data-bs-dismiss="modal"></button>

                  </div>

                  <div class="modal-body">

                      <div class="row">

                          <div class="col-md-6 mb-3">

                              <label class="form-label">

                                  Kategorija

                              </label>

                              <select
                                  name="category_id"
                                  class="form-select">

                                  <option value="">
                                      Izaberi kategoriju
                                  </option>

                                  <?php foreach ($categories as $category): ?>

                                      <option value="<?= $category['id'] ?>">

                                          <?= e($category['name']) ?>

                                      </option>

                                  <?php endforeach; ?>

                              </select>

                          </div>

                          <div class="col-md-6 mb-3">

                              <label class="form-label">

                                  Inventarski broj

                              </label>

                              <input
                                  type="text"
                                  name="inventory_number"
                                  class="form-control">

                          </div>

                      </div>

                      <div class="row">

                          <div class="col-md-6 mb-3">

                              <label class="form-label">

                                  Proizvođač

                              </label>

                              <input
                                  type="text"
                                  name="manufacturer"
                                  class="form-control">

                          </div>

                          <div class="col-md-6 mb-3">

                              <label class="form-label">

                                  Model

                              </label>

                              <input
                                  type="text"
                                  name="model"
                                  class="form-control">

                          </div>

                      </div>

                      <div class="row">

                          <div class="col-md-6 mb-3">

                              <label class="form-label">

                                  Serijski broj

                              </label>

                              <input
                                  type="text"
                                  name="serial_number"
                                  class="form-control">

                          </div>

                      </div>

                      <hr>

                      <h6>

                          Zaduženje

                      </h6>

                      <div class="mb-3">

                          <label class="form-label">

                              Zaposleni

                          </label>

                          <select
                              name="employee_id"
                              class="form-select">

                              <option value="">
                                  Nije zadužen
                              </option>

                              <?php foreach ($employees as $employee): ?>

                                  <option value="<?= $employee['id'] ?>">

                                      <?= e(
                                            $employee['first_name']
                                                . ' '
                                                . $employee['last_name']
                                                . ' ('
                                                . $employee['personal_id']
                                                . ')'
                                        ) ?>

                                  </option>

                              <?php endforeach; ?>

                          </select>

                      </div>

                      <div class="mb-3">

                          <label class="form-label">

                              Napomena

                          </label>

                          <textarea
                              name="note"
                              class="form-control"
                              rows="3"></textarea>

                      </div>

                  </div>

                  <hr>

                  <h6>

                      Dodatni atributi

                  </h6>

                  <div id="dynamic-attributes"></div>

                  <div class="modal-footer">

                      <button
                          type="submit"
                          class="btn btn-primary">

                          Sačuvaj

                      </button>

                  </div>

              </form>

          </div>

      </div>

  </div>